<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Medicine;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of units.
     */
    public function index(Request $request)
    {
        $query = Unit::withCount('medicines');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('abbreviation', 'like', '%' . $search . '%');
            });
        }

        $units = $query->latest('id')->paginate(10)->withQueryString();

        return view('master-data.units.index', compact('units'));
    }

    /**
     * Store a newly created unit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255|unique:units,name',
            'abbreviation' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Nama satuan wajib diisi.',
            'name.unique'   => 'Nama satuan ini sudah terdaftar.',
        ]);

        Unit::create($validated);

        return redirect()->route('units.index')->with('success', 'Satuan obat berhasil ditambahkan.');
    }

    /**
     * Update the specified unit.
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255|unique:units,name,' . $unit->id,
            'abbreviation' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Nama satuan wajib diisi.',
            'name.unique'   => 'Nama satuan ini sudah terdaftar.',
        ]);

        $unit->update($validated);

        return redirect()->route('units.index')->with('success', 'Satuan obat berhasil diperbarui.');
    }

    /**
     * Remove the specified unit.
     */
    public function destroy(Unit $unit)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            if ($unit->medicines()->count() > 0) {
                $unit->medicines()->delete();
            }
            
            $unit->delete();
            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('units.index')->with('success', 'Satuan obat dan data obat terkait berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('units.index')
                ->with('error', 'Satuan tidak dapat dihapus karena obat di dalamnya terhubung dengan data transaksi atau riwayat stok.');
        }
    }

    /**
     * Return JSON list of medicines for a given unit (AJAX).
     */
    public function medicines(Unit $unit)
    {
        $medicines = $unit->medicines()
            ->with(['category', 'medicineGroup'])
            ->withSum('stocks as current_stock', 'quantity')
            ->orderBy('name')
            ->get()
            ->map(function ($m) use ($unit) {
                return [
                    'name'                  => $m->name,
                    'code'                  => $m->code,
                    'selling_price'         => $m->selling_price,
                    'unit'                  => $unit->abbreviation ?? $unit->name,
                    'current_stock'         => (int) ($m->current_stock ?? 0),
                    'is_active'             => $m->is_active,
                    'requires_prescription' => $m->requires_prescription,
                ];
            });

        return response()->json([
            'label'     => $unit->name,
            'medicines' => $medicines,
        ]);
    }
}
