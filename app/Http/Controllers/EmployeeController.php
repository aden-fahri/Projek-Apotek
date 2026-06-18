<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role') && $request->role !== 'Semua Role') {
            $query->where('role', strtolower($request->role));
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $isActive = $request->status === 'Aktif' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        // Pagination
        $employees = $query->paginate(10)->withQueryString();

        // Metrics
        $totalKaryawan = User::count();
        $totalAdmin = User::where('role', 'admin')->count();
        $totalKasir = User::where('role', 'kasir')->count();

        return view('employees.index', compact('employees', 'totalKaryawan', 'totalAdmin', 'totalKasir'));
    }
}
