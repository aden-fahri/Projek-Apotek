@extends('layouts.admin')

@section('title', 'Manajemen Karyawan')

@push('styles')
    <!-- Google Fonts: Quicksand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0D9488;
            --primary-dark: #0F766E;
            --primary-light: #14B8A6;
            --background: #f4f6f8;
            --surface: #FFFFFF;
            --white: #FFFFFF;
            --text-primary: #1E293B;
            --text-secondary: #64748B;
            --success: #22C55E;
            --warning: #F59E0B;
            --danger: #EF4444;
            --border-color: #E2E8F0;
        }

        /* Header Section */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header-title-area {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .header-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .header-subtitle {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .btn-add {
            background-color: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s ease, transform 0.1s ease;
            box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.2);
            text-decoration: none;
        }

        .btn-add:hover {
            background-color: var(--primary-dark);
        }

        .btn-add:active {
            transform: scale(0.98);
        }

        /* Metric Cards Section */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        @media (max-width: 768px) {
            .metrics-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        .metric-card {
            background-color: var(--white);
            border-radius: 12px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.02);
        }

        .metric-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .metric-icon-wrapper.karyawan {
            background-color: #E6F4F1;
            color: var(--primary);
        }

        .metric-icon-wrapper.admin {
            background-color: #ECFDF5;
            color: #10B981;
        }

        .metric-icon-wrapper.kasir {
            background-color: #EFF6FF;
            color: #3B82F6;
        }

        .metric-details {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .metric-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .metric-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
        }

        /* Main Panel Box */
        .panel {
            background-color: var(--white);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Filter Section */
        .filter-form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .search-container {
            position: relative;
            width: 100%;
            max-width: 320px;
        }

        @media (max-width: 640px) {
            .search-container {
                max-width: 100%;
            }
        }

        .search-input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
            background-color: var(--white);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        .dropdown-group {
            display: flex;
            gap: 12px;
            width: auto;
        }

        @media (max-width: 640px) {
            .dropdown-group {
                width: 100%;
            }
            .dropdown-group select {
                flex: 1;
            }
        }

        .select-filter {
            padding: 10px 36px 10px 16px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
            background-color: var(--white);
            outline: none;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2.5'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 14px;
            min-width: 140px;
            transition: border-color 0.2s ease;
        }

        .select-filter:focus {
            border-color: var(--primary);
        }

        /* Table CSS */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 8px;
        }

        .employee-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .employee-table th {
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 700;
            color: var(--primary);
            border-bottom: 2px solid #F1F5F9;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background-color: #FAF8F4;
            white-space: nowrap;
        }

        .employee-table td {
            padding: 16px;
            font-size: 14px;
            color: var(--text-primary);
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            white-space: nowrap;
        }

        .employee-table tbody tr {
            transition: background-color 0.15s ease;
        }

        .employee-table tbody tr:hover {
            background-color: #F8FAFC;
        }

        .col-id {
            font-weight: 600;
            color: var(--text-secondary);
            width: 50px;
        }

        .col-name {
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Badges */
        .badge-role {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
            text-transform: capitalize;
        }

        .badge-role.admin {
            background-color: #E6F4F1;
            color: var(--primary);
        }

        .badge-role.kasir {
            background-color: #EBF3FE;
            color: #2563EB;
        }

        /* Status indicator */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-indicator.aktif {
            color: #16A34A;
        }

        .status-indicator.aktif .status-dot {
            background-color: var(--success);
        }

        .status-indicator.nonaktif {
            color: #DC2626;
        }

        .status-indicator.nonaktif .status-dot {
            background-color: var(--danger);
        }

        /* Action Buttons */
        .action-cell {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            background: none;
            border: none;
            padding: 6px;
            border-radius: 6px;
            cursor: pointer;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s ease, color 0.2s ease;
            text-decoration: none;
        }

        .btn-action:hover {
            background-color: #F1F5F9;
        }

        .btn-action.edit:hover {
            color: var(--primary);
        }

        .btn-action.delete:hover {
            color: var(--danger);
        }

        /* Footer & Pagination */
        .panel-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 4px;
        }

        .footer-info {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 6px;
            list-style: none;
        }

        .page-link {
            min-width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background-color: var(--white);
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
            text-decoration: none;
            padding: 0 8px;
        }

        .page-link:hover:not(.disabled):not(.active) {
            border-color: var(--primary);
            color: var(--primary);
            background-color: #FAF8F4;
        }

        .page-link.active {
            background-color: var(--primary);
            border-color: var(--primary);
            color: var(--white);
            box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.15);
        }

        .page-link.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Toast Notification */
        .toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            z-index: 9999;
        }

        .toast {
            background-color: var(--white);
            border-radius: 8px;
            padding: 16px 20px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 320px;
            max-width: 400px;
            transform: translateX(120%);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
            opacity: 0;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast-success {
            border-left-color: var(--success);
        }

        .toast-danger {
            border-left-color: var(--danger);
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            font-size: 14px;
            color: var(--text-primary);
        }

        .toast-message {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .toast-close:hover {
            background-color: #F1F5F9;
            color: var(--text-primary);
        }

        /* Password wrapper */
        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            border-radius: 4px;
            transition: color 0.2s ease;
            z-index: 10;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .password-toggle .icon-eye {
            display: none;
        }

        .password-toggle.active .icon-eye {
            display: block;
        }

        .password-toggle.active .icon-eye-off {
            display: none;
        }
    </style>
@endpush

@section('content')
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer">
        @if($errors->any())
        <div class="toast toast-danger show" id="errorToast">
            <div class="toast-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
            <div class="toast-content">
                <div class="toast-title">Kesalahan</div>
                <div class="toast-message">
                    <ul style="margin: 4px 0 0 0; padding-left: 16px; font-size: 13px; text-align: left; list-style-type: disc;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="toast-close" onclick="closeToast('errorToast')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        @endif

        @if(session('success'))
        <div class="toast toast-success show" id="successToast">
            <div class="toast-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <div class="toast-content">
                <div class="toast-title">Berhasil</div>
                <div class="toast-message">{{ session('success') }}</div>
            </div>
            <button type="button" class="toast-close" onclick="closeToast('successToast')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        @endif
    </div>

    <div class="space-y-6">
        <!-- Header Section -->
        <div class="header-section">
            <div class="header-title-area">
                <h1 class="header-title">Manajemen Karyawan</h1>
                <p class="header-subtitle">Kelola akun dan akses karyawan apotek</p>
            </div>
            <button class="btn-add" onclick="openCreateModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Karyawan
            </button>
        </div>

        <!-- Metric Cards -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-icon-wrapper karyawan">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div class="metric-details">
                    <span class="metric-label">Total Karyawan</span>
                    <span class="metric-value">{{ $totalKaryawan }}</span>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon-wrapper admin">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
                <div class="metric-details">
                    <span class="metric-label">Total Admin</span>
                    <span class="metric-value">{{ $totalAdmin }}</span>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon-wrapper kasir">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                </div>
                <div class="metric-details">
                    <span class="metric-label">Total Kasir</span>
                    <span class="metric-value">{{ $totalKasir }}</span>
                </div>
            </div>
        </div>

        <!-- Main Panel -->
        <div class="panel">
            <!-- Filter Bar -->
            <form action="{{ route('employees.index') }}" method="GET" class="filter-form">
                <div class="search-container">
                    <span class="search-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <input type="text" name="search" class="search-input" placeholder="Cari Nama / Email..." value="{{ request('search') }}" onchange="this.form.submit()">
                </div>
                <div class="dropdown-group">
                    <select name="role" class="select-filter" onchange="this.form.submit()">
                        <option value="Semua Role" {{ request('role') == 'Semua Role' ? 'selected' : '' }}>Semua Role</option>
                        <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Kasir" {{ request('role') == 'Kasir' ? 'selected' : '' }}>Kasir</option>
                    </select>
                    <select name="status" class="select-filter" onchange="this.form.submit()">
                        <option value="Semua Status" {{ request('status') == 'Semua Status' ? 'selected' : '' }}>Semua Status</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </form>

            <!-- Table responsive container -->
            <div class="table-responsive">
                <table class="employee-table">
                    <thead>
                        <tr>
                            <th class="col-id">#</th>
                            <th>Nama Karyawan</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $index => $employee)
                        <tr>
                            <td class="col-id">{{ $employees->firstItem() + $index }}</td>
                            <td class="col-name">{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>
                                <span class="badge-role {{ $employee->role == 'admin' ? 'admin' : 'kasir' }}">
                                    {{ ucfirst($employee->role) }}
                                </span>
                            </td>
                            <td>{{ $employee->phone ?? '-' }}</td>
                            <td>
                                @if($employee->is_active)
                                <span class="status-indicator aktif">
                                    <span class="status-dot"></span>
                                    Aktif
                                </span>
                                @else
                                <span class="status-indicator nonaktif">
                                    <span class="status-dot"></span>
                                    Nonaktif
                                </span>
                                @endif
                            </td>
                            <td>{{ $employee->created_at ? $employee->created_at->format('d M Y') : '-' }}</td>
                            <td>
                                <div class="action-cell">
                                    <form action="{{ route('employees.toggle-status', $employee->id) }}" method="POST" style="margin: 0; display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-action {{ $employee->is_active ? 'delete' : 'edit' }}" title="{{ $employee->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Akun">
                                            @if($employee->is_active)
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 9.9-1"></path></svg>
                                            @endif
                                        </button>
                                    </form>

                                    <button type="button" class="btn-action edit" title="Edit Karyawan"
                                            data-id="{{ $employee->id }}"
                                            data-name="{{ $employee->name }}"
                                            data-username="{{ $employee->username }}"
                                            data-role="{{ $employee->role }}"
                                            data-telepon="{{ $employee->phone }}"
                                            data-alamat="{{ $employee->address }}"
                                            onclick="openEditModal(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    </button>
                                    
                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="confirmDelete(event, this, 'Hapus Pengguna?', 'Apakah Anda yakin ingin menghapus pengguna {{ addslashes($employee->name) }}?')" style="margin: 0; display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action delete" title="Hapus Karyawan">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="empty-state">Data karyawan tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination area -->
            @if($employees->hasPages())
            <div class="panel-footer">
                <span class="footer-info">Menampilkan {{ $employees->firstItem() }} - {{ $employees->lastItem() }} dari {{ $employees->total() }} karyawan</span>
                
                <div class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($employees->onFirstPage())
                        <span class="page-link disabled">&lt;</span>
                    @else
                        <a href="{{ $employees->previousPageUrl() }}" class="page-link">&lt;</a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($employees->links()->elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="page-link disabled">{{ $element }}</span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $employees->currentPage())
                                    <span class="page-link active">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($employees->hasMorePages())
                        <a href="{{ $employees->nextPageUrl() }}" class="page-link">&gt;</a>
                    @else
                        <span class="page-link disabled">&gt;</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- CREATE EMPLOYEE MODAL -->
    <div class="modal-overlay" id="create-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; width: 100%; max-width: 500px; padding: 24px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 16px;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #1f2937;">Tambah Karyawan Baru</h3>
                <button type="button" onclick="closeCreateModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #6b7280;">&times;</button>
            </div>
            
            <form action="{{ route('employees.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151;">Nama Lengkap <span style="color:red;">*</span></label>
                        <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;" value="{{ old('name') }}">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151;">Username <span style="color:red;">*</span></label>
                        <input type="text" name="username" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;" value="{{ old('username') }}">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151;">Role <span style="color:red;">*</span></label>
                            <select name="role" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
                                <option value="kasir">Kasir</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151;">No. Telepon <span style="color: #64748b; font-weight: normal; font-size: 13px;">(Opsional)</span></label>
                            <input type="text" name="telepon" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;" value="{{ old('telepon') }}">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151;">Password <span style="color:red;">*</span></label>
                            <div class="password-wrapper">
                                <input type="password" id="create_password" name="password" required style="width: 100%; padding: 10px 40px 10px 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
                                <button type="button" class="password-toggle" onclick="togglePassword('create_password', this)" aria-label="Tampilkan password" style="right: 8px;">
                                    <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151;">Konfirmasi Password <span style="color:red;">*</span></label>
                            <div class="password-wrapper">
                                <input type="password" id="create_password_confirmation" name="password_confirmation" required style="width: 100%; padding: 10px 40px 10px 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
                                <button type="button" class="password-toggle" onclick="togglePassword('create_password_confirmation', this)" aria-label="Tampilkan konfirmasi password" style="right: 8px;">
                                    <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151;">Alamat <span style="color: #64748b; font-weight: normal; font-size: 13px;">(Opsional)</span></label>
                        <textarea name="alamat" rows="2" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; resize: vertical;">{{ old('alamat') }}</textarea>
                    </div>
                    
                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 16px; border-top: 1px solid #e5e7eb; padding-top: 16px;">
                        <button type="button" onclick="closeCreateModal()" style="padding: 10px 16px; border: 1px solid #d1d5db; background: white; border-radius: 6px; cursor: pointer; font-weight: 500;">Batal</button>
                        <button type="submit" style="padding: 10px 16px; border: none; background: #0D9488; color: white; border-radius: 6px; cursor: pointer; font-weight: 500;">Simpan Karyawan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT EMPLOYEE MODAL -->
    <div class="modal-overlay" id="edit-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 12px; width: 100%; max-width: 500px; padding: 24px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 16px;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #1f2937;">Ubah Data Karyawan</h3>
                <button type="button" onclick="closeEditModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #6b7280;">&times;</button>
            </div>
            
            <form action="" method="POST" id="edit-form">
                @csrf
                @method('PUT')
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151;">Nama Lengkap <span style="color:red;">*</span></label>
                        <input type="text" name="name" id="edit-name" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151;">Username <span style="color:red;">*</span></label>
                        <input type="text" name="username" id="edit-username" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151;">Role <span style="color:red;">*</span></label>
                            <select name="role" id="edit-role" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
                                <option value="kasir">Kasir</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151;">No. Telepon <span style="color: #64748b; font-weight: normal; font-size: 13px;">(Opsional)</span></label>
                            <input type="text" name="telepon" id="edit-telepon" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151;">Password Baru <span style="color: #64748b; font-weight: normal; font-size: 13px;">(Opsional)</span></label>
                            <div class="password-wrapper">
                                <input type="password" id="edit_password" name="password" style="width: 100%; padding: 10px 40px 10px 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
                                <button type="button" class="password-toggle" onclick="togglePassword('edit_password', this)" aria-label="Tampilkan password" style="right: 8px;">
                                    <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151;">Konfirmasi Password</label>
                            <div class="password-wrapper">
                                <input type="password" id="edit_password_confirmation" name="password_confirmation" style="width: 100%; padding: 10px 40px 10px 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
                                <button type="button" class="password-toggle" onclick="togglePassword('edit_password_confirmation', this)" aria-label="Tampilkan konfirmasi password" style="right: 8px;">
                                    <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: #374151;">Alamat <span style="color: #64748b; font-weight: normal; font-size: 13px;">(Opsional)</span></label>
                        <textarea name="alamat" id="edit-alamat" rows="2" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; resize: vertical;"></textarea>
                    </div>
                    
                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 16px; border-top: 1px solid #e5e7eb; padding-top: 16px;">
                        <button type="button" onclick="closeEditModal()" style="padding: 10px 16px; border: 1px solid #d1d5db; background: white; border-radius: 6px; cursor: pointer; font-weight: 500;">Batal</button>
                        <button type="submit" style="padding: 10px 16px; border: none; background: #0D9488; color: white; border-radius: 6px; cursor: pointer; font-weight: 500;">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    btn.classList.add('active');
                } else {
                    input.type = 'password';
                    btn.classList.remove('active');
                }
            }
        }

        function closeToast(id) {
            const toast = document.getElementById(id);
            if (toast) {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }
        }

        // Auto close toast after 5 seconds
        document.addEventListener('DOMContentLoaded', () => {
            const toasts = document.querySelectorAll('.toast');
            toasts.forEach(toast => {
                setTimeout(() => {
                    if (toast) {
                        toast.classList.remove('show');
                    }
                }, 5000);
            });
        });

        // Modal Functions
        function openCreateModal() {
            document.getElementById('create-modal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeCreateModal() {
            document.getElementById('create-modal').style.display = 'none';
            document.body.style.overflow = '';
        }

        function openEditModal(btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const username = btn.getAttribute('data-username');
            const role = btn.getAttribute('data-role');
            const telepon = btn.getAttribute('data-telepon');
            const alamat = btn.getAttribute('data-alamat');

            document.getElementById('edit-name').value = name;
            document.getElementById('edit-username').value = username;
            document.getElementById('edit-role').value = role;
            document.getElementById('edit-telepon').value = telepon || '';
            document.getElementById('edit-alamat').value = alamat || '';
            
            document.getElementById('edit-form').action = `/employees/${id}`;

            document.getElementById('edit-modal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('edit-modal').style.display = 'none';
            document.body.style.overflow = '';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const createModal = document.getElementById('create-modal');
            const editModal = document.getElementById('edit-modal');
            if (event.target == createModal) {
                closeCreateModal();
            } else if (event.target == editModal) {
                closeEditModal();
            }
        }
    </script>
@endpush


