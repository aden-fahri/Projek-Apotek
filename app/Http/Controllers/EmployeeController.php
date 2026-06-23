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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,kasir',
            'telepon'  => ['nullable', 'string', 'max:20', 'regex:/^08[0-9]{8,13}$/'],
            'alamat'   => 'nullable|string|max:500',
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username ini sudah terdaftar. Gunakan username lain.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required'      => 'Role wajib dipilih.',
            'role.in'            => 'Role harus Admin atau Kasir.',
            'telepon.regex'      => 'Nomor telepon harus diawali dengan 08 dan hanya berisi angka.',
        ]);

        User::create([
            'name'      => $validated['name'],
            'username'  => $validated['username'],
            'email'     => $validated['username'] . '@apotek.local', // Auto-generate dummy email
            'password'  => $validated['password'],
            'role'      => $validated['role'],
            'telepon'   => $validated['telepon'] ?? null,
            'alamat'    => $validated['alamat'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('employees.index')->with('success', 'Karyawan baru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $employee->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'required|in:admin,kasir',
            'telepon'  => ['nullable', 'string', 'max:20', 'regex:/^08[0-9]{8,13}$/'],
            'alamat'   => 'nullable|string|max:500',
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username ini sudah terdaftar. Gunakan username lain.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required'      => 'Role wajib dipilih.',
            'role.in'            => 'Role harus Admin atau Kasir.',
            'telepon.regex'      => 'Nomor telepon harus diawali dengan 08 dan hanya berisi angka.',
        ]);

        $data = [
            'name'    => $validated['name'],
            'username'=> $validated['username'],
            'role'    => $validated['role'],
            'telepon' => $validated['telepon'] ?? null,
            'alamat'  => $validated['alamat'] ?? null,
        ];

        // Only update password if filled
        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employee)
    {
        if (auth()->id() === $employee->id) {
            return redirect()->route('employees.index')->withErrors(['Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan.']);
        }

        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
