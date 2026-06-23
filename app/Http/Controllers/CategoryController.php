<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('medicines');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $categories = $query->latest('id')->paginate(10)->withQueryString();

        return view('master-data.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Nama kategori ini sudah terdaftar.',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori obat berhasil ditambahkan.');
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Nama kategori ini sudah terdaftar.',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori obat berhasil diperbarui.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        if ($category->medicines()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data obat.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori obat berhasil dihapus.');
    }

    /**
     * Return JSON list of medicines for a given category (AJAX).
     */
    public function medicines(Category $category)
    {
        $medicines = $category->medicines()
            ->with(['unit', 'medicineGroup'])
            ->withSum('stocks as current_stock', 'quantity')
            ->orderBy('name')
            ->get()
            ->map(function ($m) {
                return [
                    'name'                  => $m->name,
                    'code'                  => $m->code,
                    'selling_price'         => $m->selling_price,
                    'unit'                  => optional($m->unit)->abbreviation ?? optional($m->unit)->name ?? '-',
                    'current_stock'         => (int) ($m->current_stock ?? 0),
                    'is_active'             => $m->is_active,
                    'requires_prescription' => $m->requires_prescription,
                ];
            });

        return response()->json([
            'label'     => $category->name,
            'medicines' => $medicines,
        ]);
    }
}
