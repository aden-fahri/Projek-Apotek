<?php

namespace App\Http\Controllers;

use App\Models\MedicineGroup;
use Illuminate\Http\Request;

class MedicineGroupController extends Controller
{
    /**
     * Display a listing of medicine groups.
     */
    public function index(Request $request)
    {
        $query = MedicineGroup::withCount('medicines');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $groups = $query->latest('id')->paginate(10)->withQueryString();

        return view('master-data.medicine-groups.index', compact('groups'));
    }

    /**
     * Store a newly created medicine group.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:medicine_groups,name',
            'code'        => 'nullable|string|max:10|unique:medicine_groups,code',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama golongan wajib diisi.',
            'name.unique'   => 'Nama golongan ini sudah terdaftar.',
            'code.unique'   => 'Kode golongan ini sudah digunakan.',
        ]);

        MedicineGroup::create($validated);

        return redirect()->route('medicine-groups.index')->with('success', 'Golongan obat berhasil ditambahkan.');
    }

    /**
     * Update the specified medicine group.
     */
    public function update(Request $request, MedicineGroup $medicineGroup)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:medicine_groups,name,' . $medicineGroup->id,
            'code'        => 'nullable|string|max:10|unique:medicine_groups,code,' . $medicineGroup->id,
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama golongan wajib diisi.',
            'name.unique'   => 'Nama golongan ini sudah terdaftar.',
            'code.unique'   => 'Kode golongan ini sudah digunakan.',
        ]);

        $medicineGroup->update($validated);

        return redirect()->route('medicine-groups.index')->with('success', 'Golongan obat berhasil diperbarui.');
    }

    /**
     * Remove the specified medicine group.
     */
    public function destroy(MedicineGroup $medicineGroup)
    {
        if ($medicineGroup->medicines()->count() > 0) {
            return redirect()->route('medicine-groups.index')
                ->with('error', 'Golongan tidak dapat dihapus karena masih digunakan oleh data obat.');
        }

        $medicineGroup->delete();

        return redirect()->route('medicine-groups.index')->with('success', 'Golongan obat berhasil dihapus.');
    }
}
