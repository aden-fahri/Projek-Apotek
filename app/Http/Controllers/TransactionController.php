<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineStock;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Category;
use App\Models\PharmacySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Tampilkan halaman POS (Sistem Kasir)
     */
    public function pos(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        // Load initial medicines for display
        $medicinesQuery = Medicine::where('is_active', true)
            ->with('category')
            ->select('medicines.*');
            
        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $medicinesQuery->where('category_id', $request->category);
        }

        // Get medicines and attach their total available stock
        $medicines = $medicinesQuery->limit(20)->get()->map(function ($medicine) {
            $totalStock = DB::table('medicine_stocks')
                            ->where('medicine_id', $medicine->id)
                            ->where('status', 'available')
                            ->where('expiry_date', '>', now())
                            ->sum('quantity');
            $medicine->current_stock = $totalStock;
            return $medicine;
        });

        // Ambil data apotek untuk struk
        $setting = PharmacySetting::getSetting();

        return view('transactions.pos', compact('categories', 'medicines', 'setting'));
    }

    /**
     * Endpoint API untuk pencarian obat di POS
     */
    public function searchMedicine(Request $request)
    {
        $query = $request->input('q');
        $categoryId = $request->input('category');

        $medicines = Medicine::where('is_active', true)
            ->with('category');

        if (!empty($query)) {
            $medicines->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('code', 'like', "%{$query}%");
            });
        }

        if (!empty($categoryId)) {
            $medicines->where('category_id', $categoryId);
        }

        $medicines = $medicines->limit(24)->get()->map(function ($medicine) {
            $totalStock = DB::table('medicine_stocks')
                            ->where('medicine_id', $medicine->id)
                            ->where('status', 'available')
                            ->where('expiry_date', '>', now())
                            ->sum('quantity');
            $medicine->current_stock = $totalStock;
            return $medicine;
        });

        return response()->json($medicines);
    }

    /**
     * Proses simpan transaksi dari POS
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:Tunai,QRIS,Transfer',
            'paid_amount' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $itemsData = [];

            // 1. Validasi & Kalkulasi Subtotal
            foreach ($request->items as $item) {
                $medicine = Medicine::findOrFail($item['id']);
                
                // Cek stok
                $totalStock = DB::table('medicine_stocks')
                    ->where('medicine_id', $medicine->id)
                    ->where('status', 'available')
                    ->where('expiry_date', '>', now())
                    ->sum('quantity');

                if ($totalStock < $item['quantity']) {
                    throw new \Exception("Stok tidak mencukupi untuk {$medicine->name}. Sisa: {$totalStock}");
                }

                $itemSubtotal = $medicine->selling_price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'medicine' => $medicine,
                    'quantity' => $item['quantity'],
                    'price' => $medicine->selling_price,
                    'purchase_price' => $medicine->purchase_price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Simulasi Tax (misal 0% sementara, atau ambil dari setting jika ada)
            $taxRate = 0; // 0%
            $taxAmount = $subtotal * $taxRate;
            $grandTotal = $subtotal + $taxAmount;

            if ($request->payment_method == 'Tunai' && $request->paid_amount < $grandTotal) {
                 throw new \Exception("Jumlah bayar kurang dari total belanja.");
            }

            $changeAmount = max(0, $request->paid_amount - $grandTotal);

            // 2. Generate Invoice Number (INV-YYYYMMDD-XXX)
            $datePrefix = date('Ymd');
            $lastTrx = Transaction::where('invoice_number', 'like', "INV-{$datePrefix}-%")->orderBy('id', 'desc')->first();
            $sequence = $lastTrx ? (int) substr($lastTrx->invoice_number, -3) + 1 : 1;
            $invoiceNumber = "INV-{$datePrefix}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);

            // 3. Simpan Header Transaksi
            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => Auth::id(),
                'transaction_date' => now(),
                'total' => $subtotal,
                'tax' => $taxAmount,
                'grand_total' => $grandTotal,
                'paid_amount' => $request->paid_amount,
                'change_amount' => $changeAmount,
                'payment_method' => $request->payment_method,
                'customer_name' => $request->customer_name,
                'notes' => $request->notes,
                'status' => 'completed',
            ]);

            // 4. Simpan Detail & Potong Stok (FIFO)
            foreach ($itemsData as $data) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'medicine_id' => $data['medicine']->id,
                    'quantity' => $data['quantity'],
                    'price' => $data['price'],
                    'subtotal' => $data['subtotal'],
                ]);

                // Logic Pengurangan Stok FIFO
                $qtyToDeduct = $data['quantity'];
                
                // Ambil stok batch yang expired-nya paling dekat (FIFO) dan masih available
                $stocks = MedicineStock::where('medicine_id', $data['medicine']->id)
                    ->where('status', 'available')
                    ->where('expiry_date', '>', now())
                    ->where('quantity', '>', 0)
                    ->orderBy('expiry_date', 'asc')
                    ->lockForUpdate() // Cegah race condition
                    ->get();

                foreach ($stocks as $stock) {
                    if ($qtyToDeduct <= 0) break;

                    if ($stock->quantity >= $qtyToDeduct) {
                        // Batch ini cukup
                        $stock->quantity -= $qtyToDeduct;
                        $stock->save();
                        $qtyToDeduct = 0;
                    } else {
                        // Batch ini tidak cukup, kurangi semua, lanjut ke batch berikutnya
                        $qtyToDeduct -= $stock->quantity;
                        $stock->quantity = 0;
                        $stock->save();
                    }
                }
                
                if ($qtyToDeduct > 0) {
                    // Seharusnya tidak terjadi karena sudah divalidasi awal, tapi untuk safety net:
                    throw new \Exception("Stok {$data['medicine']->name} tiba-tiba habis saat proses FIFO.");
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'invoice' => $transaction->invoice_number,
                'transaction_id' => $transaction->id,
                'change' => $changeAmount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Tampilkan riwayat transaksi
     */
    public function history(Request $request)
    {
        $query = Transaction::with(['kasir', 'details.medicine'])
            ->orderBy('transaction_date', 'desc');

        // Kasir hanya melihat transaksinya sendiri
        if (Auth::user()->role === 'kasir') {
            $query->where('user_id', Auth::id());
        }

        // Filter tanggal
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        $transactions = $query->paginate(15);

        return view('transactions.history', compact('transactions'));
    }

    /**
     * Admin membatalkan transaksi
     */
    public function cancelTransaction(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return back()->with('error', 'Hanya Admin yang dapat membatalkan transaksi.');
        }

        $transaction = Transaction::with('details')->findOrFail($id);

        if ($transaction->status === 'cancelled') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
        }

        try {
            DB::beginTransaction();

            // Ubah status
            $transaction->status = 'cancelled';
            $transaction->save();

            // Kembalikan stok
            // Kita kembalikan stok ke batch terbaru yang expiration_date-nya belum lewat
            foreach ($transaction->details as $detail) {
                // Cari stock batch yang paling baru / expiry terjauh untuk obat ini
                $stock = MedicineStock::where('medicine_id', $detail->medicine_id)
                    ->orderBy('expiry_date', 'desc')
                    ->first();

                if ($stock) {
                    $stock->quantity += $detail->quantity;
                    $stock->save();
                } else {
                    // Jika tidak ada data stok sama sekali (terhapus), buat record stok baru?
                    // Karena ini return, kita biarkan saja atau log error. 
                    // Kita asumsikan data obat masih ada.
                    MedicineStock::create([
                        'medicine_id' => $detail->medicine_id,
                        'batch_number' => 'RTN-' . $transaction->invoice_number,
                        'expiry_date' => now()->addYears(1), // Asumsi aman
                        'quantity' => $detail->quantity,
                        'purchase_price' => $detail->purchase_price,
                        'status' => 'available'
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
