<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen Karyawan - MediFlow</title>
    
    <!-- Google Fonts: Quicksand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0D9488;
            --primary-dark: #0F766E;
            --primary-light: #14B8A6;
            --background: #F5F0E8;
            --surface: #FAF8F4;
            --white: #FFFFFF;
            --text-primary: #1E293B;
            --text-secondary: #64748B;
            --success: #22C55E;
            --warning: #F59E0B;
            --danger: #EF4444;
            --border-color: #E2E8F0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--background);
            font-family: 'Quicksand', sans-serif;
            color: var(--text-primary);
            min-height: 100vh;
            padding: 40px 24px;
            display: flex;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 1120px;
            display: flex;
            flex-direction: column;
            gap: 24px;
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

        .btn-action.toggle:hover {
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
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="header-title-area">
                <h1 class="header-title">Manajemen Karyawan</h1>
                <p class="header-subtitle">Kelola akun dan akses karyawan apotek</p>
            </div>
            <a href="#" class="btn-add">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Karyawan
            </a>
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
                                    <a href="#" class="btn-action edit" title="Edit Karyawan">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    </a>
                                    <a href="#" class="btn-action toggle" title="Ubah Status Keaktifan">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="5" width="22" height="14" rx="7" ry="7"></rect><circle cx="16" cy="12" r="3"></circle></svg>
                                    </a>
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
</body>
</html>
