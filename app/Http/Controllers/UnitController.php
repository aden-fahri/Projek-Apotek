<?php

namespace App\Http\Controllers;

use App\Models\Unit;
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
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('abbreviation', 'like', "%{$search}%");
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
            'name'         => 'required|string|max:100|unique:units,name',
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
            'name'         => 'required|string|max:100|unique:units,name,' . $unit->id,
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
        if ($unit->medicines()->count() > 0) {
            return redirect()->route('units.index')
                ->with('error', 'Satuan tidak dapat dihapus karena masih digunakan oleh data obat.');
        }

        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Satuan obat berhasil dihapus.');
    }
}
