<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineStock;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\StockReturn;
use App\Models\StockReturnDetail;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Helper to log activity to audit trail
     */
    private function logActivity($activity, $action, $detail = null)
    {
        DB::table('activity_logs')->insert([
            'user_id'    => Auth::id(),
            'activity'   => $activity,
            'module'     => 'inventory',
            'action'     => $action,
            'detail'     => $detail ? json_encode($detail) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * F-09: Data Obat (Master Data)
     */
    public function medicineIndex(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $query = Medicine::with(['category', 'medicineGroup', 'unit'])
            ->orderBy('name', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        $medicines = $query->paginate(10)->withQueryString();

        // Get active categories for filter dropdown
        $categories = DB::table('categories')->select('name')->get();

        // Get categories, groups, and units for Add/Edit forms
        $categoriesList = DB::table('categories')->select('id', 'name')->orderBy('name')->get();
        $groupsList = DB::table('medicine_groups')->select('id', 'name')->orderBy('name')->get();
        $unitsList = DB::table('units')->select('id', 'name')->orderBy('name')->get();

        // Stats for master obat
        $stats = [
            'total_jenis'   => Medicine::count(),
            'resep_wajib'   => Medicine::where('requires_prescription', true)->count(),
            'aktif'         => Medicine::where('is_active', true)->count(),
            'non_aktif'     => Medicine::where('is_active', false)->count(),
        ];

        return view('inventory.stok.master', compact(
            'medicines', 'categories', 'stats', 'search', 'category',
            'categoriesList', 'groupsList', 'unitsList'
        ));
    }

    /**
     * F-11: Lihat Stok Obat
     */
    public function stockIndex(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        // Base query from database view v_medicine_stock_summary joined with medicines table
        $query = DB::table('v_medicine_stock_summary')
            ->join('medicines', 'v_medicine_stock_summary.medicine_id', '=', 'medicines.id')
            ->select(
                'v_medicine_stock_summary.*',
                'medicines.category_id',
                'medicines.group_id',
                'medicines.unit_id',
                'medicines.description',
                'medicines.requires_prescription'
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('v_medicine_stock_summary.medicine_name', 'like', "%{$search}%")
                  ->orWhere('v_medicine_stock_summary.medicine_code', 'like', "%{$search}%")
                  ->orWhere('v_medicine_stock_summary.category_name', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('v_medicine_stock_summary.category_name', $category);
        }

        $medicines = $query->paginate(10)->withQueryString();

        // Get active categories for filter dropdown
        $categories = DB::table('categories')->select('name')->get();

        // Get categories, groups, and units for Add/Edit forms
        $categoriesList = DB::table('categories')->select('id', 'name')->orderBy('name')->get();
        $groupsList = DB::table('medicine_groups')->select('id', 'name')->orderBy('name')->get();
        $unitsList = DB::table('units')->select('id', 'name')->orderBy('name')->get();

        // Get batches details for these medicines
        $medicineIds = collect($medicines->items())->pluck('medicine_id')->toArray();
        $batches = MedicineStock::whereIn('medicine_id', $medicineIds)
            ->where('status', 'available')
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get()
            ->groupBy('medicine_id');

        // Header statistics
        $stats = [
            'total_jenis'   => Medicine::where('is_active', true)->count(),
            'stok_menipis'  => DB::table('v_medicine_stock_summary')->where('stock_status', 'Stok Rendah')->count(),
            'kadaluwarsa_30'=> MedicineStock::where('status', 'available')
                                ->where('quantity', '>', 0)
                                ->where('expiry_date', '<=', now()->addDays(30))
                                ->count(),
            'nilai_aset'    => DB::table('v_medicine_stock_summary')
                                ->select(DB::raw('SUM(total_stock * purchase_price) as total_val'))
                                ->first()->total_val ?? 0,
        ];

        return view('inventory.stok.index', compact(
            'medicines', 'categories', 'batches', 'stats', 'search', 'category',
            'categoriesList', 'groupsList', 'unitsList'
        ));
    }

    /**
     * Store a new medicine product
     */
    public function storeMedicine(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'code'                  => 'nullable|string|max:50|unique:medicines,code',
            'category_id'           => 'required|exists:categories,id',
            'group_id'              => 'required|exists:medicine_groups,id',
            'unit_id'               => 'required|exists:units,id',
            'purchase_price'        => 'required|numeric|min:0',
            'selling_price'         => 'required|numeric|min:0',
            'min_stock'             => 'required|integer|min:0',
            'description'           => 'nullable|string',
            'requires_prescription' => 'nullable|boolean',
        ]);

        // Auto-generate code if not provided
        $code = $request->code;
        if (!$code) {
            $prefix = 'OBT-';
            $latest = Medicine::orderBy('id', 'desc')->first();
            $num = $latest ? $latest->id + 1 : 1;
            $code = $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
        }

        try {
            DB::beginTransaction();

            $medicine = Medicine::create([
                'code'                  => $code,
                'name'                  => $request->name,
                'category_id'           => $request->category_id,
                'group_id'              => $request->group_id,
                'unit_id'               => $request->unit_id,
                'purchase_price'        => $request->purchase_price,
                'selling_price'         => $request->selling_price,
                'min_stock'             => $request->min_stock,
                'description'           => $request->description,
                'requires_prescription' => $request->has('requires_prescription') ? 1 : 0,
                'is_active'             => 1,
            ]);

            // Create initial stock if provided
            $initialStock = (int)$request->initial_stock;
            if ($initialStock > 0) {
                $request->validate([
                    'expiry_date' => 'required|date|after_or_equal:today',
                ]);

                MedicineStock::create([
                    'medicine_id'       => $medicine->id,
                    'purchase_order_id' => null,
                    'batch_number'      => $request->batch_number ?: 'SA-' . $code,
                    'quantity'          => $initialStock,
                    'initial_quantity'  => $initialStock,
                    'expiry_date'       => $request->expiry_date,
                    'status'            => 'available',
                ]);
            }

            $this->logActivity(
                "Menambah produk obat baru: {$medicine->name} ({$medicine->code})",
                'create',
                ['medicine_id' => $medicine->id, 'code' => $medicine->code]
            );

            DB::commit();
            return redirect()->route('data-obat')->with('success', 'Produk obat baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan produk obat: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing medicine product
     */
    public function updateMedicine(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $request->validate([
            'name'                  => 'required|string|max:255',
            'code'                  => 'required|string|max:50|unique:medicines,code,' . $id,
            'category_id'           => 'required|exists:categories,id',
            'group_id'              => 'required|exists:medicine_groups,id',
            'unit_id'               => 'required|exists:units,id',
            'purchase_price'        => 'required|numeric|min:0',
            'selling_price'         => 'required|numeric|min:0',
            'min_stock'             => 'required|integer|min:0',
            'description'           => 'nullable|string',
            'requires_prescription' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $medicine->update([
                'code'                  => $request->code,
                'name'                  => $request->name,
                'category_id'           => $request->category_id,
                'group_id'              => $request->group_id,
                'unit_id'               => $request->unit_id,
                'purchase_price'        => $request->purchase_price,
                'selling_price'         => $request->selling_price,
                'min_stock'             => $request->min_stock,
                'description'           => $request->description,
                'requires_prescription' => $request->has('requires_prescription') ? 1 : 0,
            ]);

            // Adjust stock if passed
            if ($request->has('initial_stock')) {
                $newStock = (int)$request->initial_stock;
                // Get the first active batch of this medicine
                $firstStock = MedicineStock::where('medicine_id', $id)
                    ->where('status', 'available')
                    ->first();
                
                if ($firstStock) {
                    $firstStock->update([
                        'quantity'         => $newStock,
                        'initial_quantity' => max($firstStock->initial_quantity, $newStock),
                        'batch_number'     => $request->batch_number ?: $firstStock->batch_number,
                        'expiry_date'      => $request->expiry_date ?: $firstStock->expiry_date,
                    ]);
                } elseif ($newStock > 0) {
                    $request->validate([
                        'expiry_date' => 'required|date|after_or_equal:today',
                    ]);

                    MedicineStock::create([
                        'medicine_id'       => $id,
                        'purchase_order_id' => null,
                        'batch_number'      => $request->batch_number ?: 'SA-' . $medicine->code,
                        'quantity'          => $newStock,
                        'initial_quantity'  => $newStock,
                        'expiry_date'       => $request->expiry_date,
                        'status'            => 'available',
                    ]);
                }
            }

            $this->logActivity(
                "Mengubah data produk obat: {$medicine->name} ({$medicine->code})",
                'update',
                ['medicine_id' => $medicine->id, 'code' => $medicine->code]
            );

            DB::commit();
            return redirect()->route('data-obat')->with('success', 'Data produk obat berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui produk obat: ' . $e->getMessage());
        }
    }

    /**
     * Delete a medicine product
     */
    public function destroyMedicine($id)
    {
        $medicine = Medicine::findOrFail($id);
        $name = $medicine->name;
        $code = $medicine->code;

        try {
            DB::beginTransaction();

            // First delete associated stocks
            MedicineStock::where('medicine_id', $id)->delete();

            // Then delete the medicine
            $medicine->delete();

            $this->logActivity(
                "Menghapus produk obat: {$name} ({$code})",
                'delete',
                ['medicine_id' => $id, 'code' => $code]
            );

            DB::commit();
            return redirect()->route('data-obat')->with('success', 'Produk obat berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('data-obat')->with('error', 'Gagal menghapus produk obat karena terkait dengan data transaksi.');
        }
    }

    /**
     * Store a new category (AJAX supported)
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        try {
            $category = \App\Models\Category::create([
                'name'        => $request->name,
                'description' => $request->description,
            ]);

            $this->logActivity(
                "Menambah kategori obat baru: {$category->name}",
                'create',
                ['category_id' => $category->id]
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kategori baru berhasil ditambahkan!',
                    'category' => $category
                ]);
            }

            return redirect()->route('data-obat')->with('success', 'Kategori baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan kategori: ' . $e->getMessage()
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }


    /**
     * F-10: Purchase Order (PO) - List
     */
    public function poIndex()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'details.medicine'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('inventory.po.index', compact('purchaseOrders'));
    }

    /**
     * F-10: Purchase Order (PO) - Create Form
     */
    public function poCreate()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $medicines = Medicine::where('is_active', true)->orderBy('name')->get();

        return view('inventory.po.create', compact('suppliers', 'medicines'));
    }

    /**
     * F-10: Purchase Order (PO) - Store Action
     */
    public function poStore(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date'  => 'required|date',
            'notes'       => 'nullable|string',
            'items'       => 'required|array|min:1',
            'items.*.medicine_id'   => 'required|exists:medicines,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.purchase_price'=> 'required|numeric|min:0',
            'items.*.batch_number'  => 'required|string',
            'items.*.expiry_date'   => 'required|date|after_or_equal:order_date',
        ]);

        try {
            DB::beginTransaction();

            // Auto-generate invoice number: PO-YYYYMMDD-XXX
            $today = date('Ymd');
            $count = PurchaseOrder::whereDate('created_at', today())->count() + 1;
            $invoiceNumber = 'PO-' . $today . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['purchase_price'];
            }

            // Create PO Header
            $po = PurchaseOrder::create([
                'invoice_number' => $invoiceNumber,
                'supplier_id'    => $request->supplier_id,
                'user_id'        => Auth::id(),
                'order_date'     => $request->order_date,
                'total_amount'   => $totalAmount,
                'notes'          => $request->notes,
                'status'         => 'completed', // Defaults to completed (stok langsung masuk)
            ]);

            // Create details and update stock
            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['purchase_price'];

                // PO Detail
                PurchaseOrderDetail::create([
                    'purchase_order_id' => $po->id,
                    'medicine_id'       => $item['medicine_id'],
                    'quantity'          => $item['quantity'],
                    'purchase_price'    => $item['purchase_price'],
                    'subtotal'          => $subtotal,
                    'batch_number'      => $item['batch_number'],
                    'expiry_date'       => $item['expiry_date'],
                ]);

                // Create stock batch in medicine_stocks
                MedicineStock::create([
                    'medicine_id'       => $item['medicine_id'],
                    'purchase_order_id' => $po->id,
                    'batch_number'      => $item['batch_number'],
                    'quantity'          => $item['quantity'],
                    'initial_quantity'  => $item['quantity'],
                    'expiry_date'       => $item['expiry_date'],
                    'status'            => 'available',
                ]);

                // Optionally update the medicine purchase price with the latest purchase price
                $medicine = Medicine::find($item['medicine_id']);
                if ($medicine) {
                    $medicine->update([
                        'purchase_price' => $item['purchase_price']
                    ]);
                }
            }

            $this->logActivity(
                "Membuat Pembelian (Purchase Order) baru: {$invoiceNumber}",
                'create',
                ['po_id' => $po->id, 'invoice_number' => $invoiceNumber, 'total_amount' => $totalAmount]
            );

            DB::commit();

            return redirect()->route('purchase-order')->with('success', "Pembelian {$invoiceNumber} berhasil disimpan!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pembelian: ' . $e->getMessage());
        }
    }

    /**
     * F-10: Purchase Order (PO) - Edit Form
     */
    public function poEdit($id)
    {
        $po = PurchaseOrder::with(['details.medicine'])->findOrFail($id);
        
        // Prevent editing if cancelled
        if ($po->status === 'cancelled') {
            return redirect()->route('purchase-order')->with('error', 'Tidak dapat mengubah Purchase Order yang sudah dibatalkan.');
        }

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $medicines = Medicine::where('is_active', true)->orderBy('name')->get();

        return view('inventory.po.edit', compact('po', 'suppliers', 'medicines'));
    }

    /**
     * F-10: Purchase Order (PO) - Cancel Action
     */
    public function poCancel($id)
    {
        $po = PurchaseOrder::with('details')->findOrFail($id);

        if ($po->status === 'cancelled') {
            return redirect()->route('purchase-order')->with('error', 'Purchase Order sudah dalam status batal.');
        }

        // Verify if any stock batch associated with this PO has been sold/used
        foreach ($po->details as $detail) {
            $stock = MedicineStock::where('purchase_order_id', $po->id)
                ->where('medicine_id', $detail->medicine_id)
                ->where('batch_number', $detail->batch_number)
                ->first();

            if ($stock && $stock->quantity < $stock->initial_quantity) {
                $soldQty = $stock->initial_quantity - $stock->quantity;
                return redirect()->route('purchase-order')->with('error', "Tidak dapat membatalkan Purchase Order ini karena obat {$detail->medicine->name} (Batch: {$detail->batch_number}) sudah terjual/digunakan sebanyak {$soldQty} item.");
            }
        }

        try {
            DB::beginTransaction();

            // Delete corresponding stock batches
            MedicineStock::where('purchase_order_id', $po->id)->delete();

            // Set PO status to cancelled
            $po->update(['status' => 'cancelled']);

            $this->logActivity(
                "Membatalkan Pembelian (Purchase Order): {$po->invoice_number}",
                'update',
                ['po_id' => $po->id, 'invoice_number' => $po->invoice_number, 'status' => 'cancelled']
            );

            DB::commit();

            return redirect()->route('purchase-order')->with('success', "Pembelian {$po->invoice_number} berhasil dibatalkan!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('purchase-order')->with('error', 'Terjadi kesalahan saat membatalkan pembelian: ' . $e->getMessage());
        }
    }

    /**
     * F-10: Purchase Order (PO) - Update Action
     */
    public function poUpdate(Request $request, $id)
    {
        $po = PurchaseOrder::with('details')->findOrFail($id);

        if ($po->status === 'cancelled') {
            return redirect()->route('purchase-order')->with('error', 'Tidak dapat mengubah Purchase Order yang sudah dibatalkan.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date'  => 'required|date',
            'notes'       => 'nullable|string',
            'items'       => 'required|array|min:1',
            'items.*.medicine_id'   => 'required|exists:medicines,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.purchase_price'=> 'required|numeric|min:0',
            'items.*.batch_number'  => 'required|string',
            'items.*.expiry_date'   => 'required|date|after_or_equal:order_date',
        ]);

        try {
            DB::beginTransaction();

            // We need to sync items. To preserve stock integrity, let's map existing details.
            $existingDetails = $po->details->keyBy('id');
            $submittedItems = collect($request->items);
            $submittedDetailIds = $submittedItems->pluck('id')->filter()->toArray();

            // 1. Check for deleted items
            $deletedDetails = $existingDetails->diffKeys(array_flip($submittedDetailIds));
            foreach ($deletedDetails as $deletedDetail) {
                // Check if this stock has been sold
                $stock = MedicineStock::where('purchase_order_id', $po->id)
                    ->where('medicine_id', $deletedDetail->medicine_id)
                    ->where('batch_number', $deletedDetail->batch_number)
                    ->first();

                if ($stock && $stock->quantity < $stock->initial_quantity) {
                    throw new \Exception("Tidak dapat menghapus obat {$deletedDetail->medicine->name} (Batch: {$deletedDetail->batch_number}) karena stoknya sudah digunakan/terjual.");
                }

                // Delete stock and detail
                if ($stock) {
                    $stock->delete();
                }
                $deletedDetail->delete();
            }

            // 2. Process submitted items (updates & new additions)
            $totalAmount = 0;

            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['purchase_price'];
                $totalAmount += $subtotal;

                $detailId = $item['id'] ?? null;

                if ($detailId && isset($existingDetails[$detailId])) {
                    // Updating an existing item
                    $detail = $existingDetails[$detailId];

                    // Find corresponding stock record
                    $stock = MedicineStock::where('purchase_order_id', $po->id)
                        ->where('medicine_id', $detail->medicine_id)
                        ->where('batch_number', $detail->batch_number)
                        ->first();

                    // If medicine or batch number changed, we check if old stock is used
                    $batchOrMedChanged = ($detail->medicine_id != $item['medicine_id']) || ($detail->batch_number != $item['batch_number']);
                    
                    if ($batchOrMedChanged) {
                        if ($stock && $stock->quantity < $stock->initial_quantity) {
                            throw new \Exception("Tidak dapat mengganti obat/batch untuk {$detail->medicine->name} (Batch: {$detail->batch_number}) karena stoknya sudah digunakan/terjual.");
                        }
                    }

                    // Check if new quantity is less than sold quantity
                    if ($stock) {
                        $soldQty = $stock->initial_quantity - $stock->quantity;
                        if ($item['quantity'] < $soldQty) {
                            throw new \Exception("Jumlah obat {$detail->medicine->name} tidak boleh kurang dari {$soldQty} karena {$soldQty} item sudah terjual/digunakan.");
                        }
                        
                        // Update stock record
                        $stock->update([
                            'medicine_id'  => $item['medicine_id'],
                            'batch_number' => $item['batch_number'],
                            'quantity'     => $item['quantity'] - $soldQty,
                            'initial_quantity' => $item['quantity'],
                            'expiry_date'  => $item['expiry_date'],
                        ]);
                    } else {
                        // Create stock if somehow missing
                        MedicineStock::create([
                            'medicine_id'       => $item['medicine_id'],
                            'purchase_order_id' => $po->id,
                            'batch_number'      => $item['batch_number'],
                            'quantity'          => $item['quantity'],
                            'initial_quantity'  => $item['quantity'],
                            'expiry_date'       => $item['expiry_date'],
                            'status'            => 'available',
                        ]);
                    }

                    // Update detail record
                    $detail->update([
                        'medicine_id'    => $item['medicine_id'],
                        'quantity'       => $item['quantity'],
                        'purchase_price' => $item['purchase_price'],
                        'subtotal'       => $subtotal,
                        'batch_number'   => $item['batch_number'],
                        'expiry_date'    => $item['expiry_date'],
                    ]);

                } else {
                    // Adding a new item
                    PurchaseOrderDetail::create([
                        'purchase_order_id' => $po->id,
                        'medicine_id'       => $item['medicine_id'],
                        'quantity'          => $item['quantity'],
                        'purchase_price'    => $item['purchase_price'],
                        'subtotal'          => $subtotal,
                        'batch_number'      => $item['batch_number'],
                        'expiry_date'       => $item['expiry_date'],
                    ]);

                    MedicineStock::create([
                        'medicine_id'       => $item['medicine_id'],
                        'purchase_order_id' => $po->id,
                        'batch_number'      => $item['batch_number'],
                        'quantity'          => $item['quantity'],
                        'initial_quantity'  => $item['quantity'],
                        'expiry_date'       => $item['expiry_date'],
                        'status'            => 'available',
                    ]);
                }

                // Update latest purchase price of the medicine
                $medicine = Medicine::find($item['medicine_id']);
                if ($medicine) {
                    $medicine->update([
                        'purchase_price' => $item['purchase_price']
                    ]);
                }
            }

            // 3. Update PO Header
            $po->update([
                'supplier_id'  => $request->supplier_id,
                'order_date'   => $request->order_date,
                'total_amount' => $totalAmount,
                'notes'        => $request->notes,
            ]);

            $this->logActivity(
                "Memperbarui Pembelian (Purchase Order): {$po->invoice_number}",
                'update',
                ['po_id' => $po->id, 'invoice_number' => $po->invoice_number, 'total_amount' => $totalAmount]
            );

            DB::commit();

            return redirect()->route('purchase-order')->with('success', "Pembelian {$po->invoice_number} berhasil diperbarui!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui pembelian: ' . $e->getMessage());
        }
    }

    /**
     * F-10: Purchase Order (PO) - Destroy Action (only for cancelled ones)
     */
    public function poDestroy($id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->status !== 'cancelled') {
            return redirect()->route('purchase-order')->with('error', 'Hanya Purchase Order yang sudah dibatalkan yang dapat dihapus.');
        }

        try {
            DB::beginTransaction();

            // Delete associated stock batches if any still exist
            MedicineStock::where('purchase_order_id', $po->id)->delete();

            // Delete the PO itself (cascading deletes details)
            $po->delete();

            $this->logActivity(
                "Menghapus secara permanen Pembelian (Purchase Order): {$po->invoice_number}",
                'delete',
                ['invoice_number' => $po->invoice_number]
            );

            DB::commit();

            return redirect()->route('purchase-order')->with('success', "Pembelian {$po->invoice_number} berhasil dihapus secara permanen!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('purchase-order')->with('error', 'Terjadi kesalahan saat menghapus pembelian: ' . $e->getMessage());
        }
    }

    /**
     * F-14: Return Obat - List
     */
    public function returnIndex()
    {
        $returns = StockReturn::with(['supplier', 'details.medicine'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('inventory.return.index', compact('returns'));
    }

    /**
     * F-14: Return Obat - Create Form
     */
    public function returnCreate()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('inventory.return.create', compact('suppliers'));
    }

    /**
     * API helper to fetch available stocks
     */
    public function getAvailableStocks()
    {
        $stocks = MedicineStock::with('medicine')
            ->where('status', 'available')
            ->where('quantity', '>', 0)
            ->get()
            ->map(function($stock) {
                return [
                    'id'            => $stock->id,
                    'medicine_id'   => $stock->medicine_id,
                    'name'          => $stock->medicine->name,
                    'code'          => $stock->medicine->code,
                    'batch_number'  => $stock->batch_number,
                    'quantity'      => $stock->quantity,
                    'purchase_price'=> $stock->medicine->purchase_price,
                    'expiry_date'   => $stock->expiry_date->format('Y-m-d'),
                ];
            });

        return response()->json($stocks);
    }

    /**
     * F-14: Return Obat - Store Action
     */
    public function returnStore(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'return_date' => 'required|date',
            'reason'      => 'required|in:expired,damaged,wrong_item,other',
            'notes'       => 'nullable|string',
            'items'       => 'required|array|min:1',
            'items.*.medicine_stock_id' => 'required|exists:medicine_stocks,id',
            'items.*.quantity'          => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Auto-generate return number: RTN-YYYYMMDD-XXX
            $today = date('Ymd');
            $count = StockReturn::whereDate('created_at', today())->count() + 1;
            $returnNumber = 'RTN-' . $today . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            $totalAmount = 0;
            $detailsToSave = [];

            foreach ($request->items as $item) {
                $stock = MedicineStock::with('medicine')->find($item['medicine_stock_id']);

                // Validate quantity
                if ($item['quantity'] > $stock->quantity) {
                    throw new \Exception("Jumlah return untuk obat {$stock->medicine->name} (Batch: {$stock->batch_number}) melebihi stok yang tersedia ({$stock->quantity})!");
                }

                $purchasePrice = $stock->medicine->purchase_price;
                $subtotal = $item['quantity'] * $purchasePrice;
                $totalAmount += $subtotal;

                $detailsToSave[] = [
                    'medicine_id'       => $stock->medicine_id,
                    'medicine_stock_id' => $stock->id,
                    'quantity'          => $item['quantity'],
                    'purchase_price'    => $purchasePrice,
                    'subtotal'          => $subtotal,
                    'stock_record'      => $stock,
                ];
            }

            // Create Return Header
            $return = StockReturn::create([
                'return_number' => $returnNumber,
                'supplier_id'   => $request->supplier_id,
                'user_id'       => Auth::id(),
                'return_date'   => $request->return_date,
                'reason'        => $request->reason,
                'total_amount'  => $totalAmount,
                'notes'         => $request->notes,
                'status'        => 'completed', // Defaults to completed (stok langsung terpotong)
            ]);

            // Save details and deduct stock
            foreach ($detailsToSave as $detail) {
                StockReturnDetail::create([
                    'stock_return_id'   => $return->id,
                    'medicine_id'       => $detail['medicine_id'],
                    'medicine_stock_id' => $detail['medicine_stock_id'],
                    'quantity'          => $detail['quantity'],
                    'purchase_price'    => $detail['purchase_price'],
                    'subtotal'          => $detail['subtotal'],
                ]);

                // Deduct stock quantity
                $stock = $detail['stock_record'];
                $newQty = $stock->quantity - $detail['quantity'];
                
                $updateData = ['quantity' => $newQty];
                
                // If quantity becomes 0, optionally change status
                if ($newQty == 0) {
                    $updateData['status'] = 'returned';
                }

                $stock->update($updateData);
            }

            $this->logActivity(
                "Membuat Pengembalian (Return Obat) baru: {$returnNumber}",
                'create',
                ['return_id' => $return->id, 'return_number' => $returnNumber, 'total_amount' => $totalAmount]
            );

            DB::commit();

            return redirect()->route('return-obat')->with('success', "Return {$returnNumber} berhasil disimpan!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
