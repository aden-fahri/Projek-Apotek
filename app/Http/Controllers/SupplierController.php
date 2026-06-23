<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Search by name, contact person, or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status (is_active)
        if ($request->filled('status') && $request->status !== 'Semua') {
            $isActive = $request->status === 'Aktif' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        // Paginate results (10 items per page as requested)
        $suppliers = $query->latest('id')->paginate(10)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^08[0-9]{8,13}$/'],
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
        ], [
            'phone.regex' => 'Nomor telepon harus diawali dengan 08 dan hanya berisi angka.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->is_active : true;

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^08[0-9]{8,13}$/'],
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
        ], [
            'phone.regex' => 'Nomor telepon harus diawali dengan 08 dan hanya berisi angka.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->is_active : $supplier->is_active;

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }

    /**
     * Toggle the active status of the supplier.
     */
    public function toggleStatus(Supplier $supplier)
    {
        $supplier->update([
            'is_active' => !$supplier->is_active
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Status keaktifan supplier berhasil diubah.');
    }
}
