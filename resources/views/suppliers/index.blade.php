<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Supplier - MediFlow</title>
    
    <!-- Google Fonts: Quicksand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS stylesheet loaded using asset() helper -->
    <link rel="stylesheet" href="{{ asset('css/suppliers.css') }}">
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="header-title-area">
                <h1 class="header-title">Kelola Supplier</h1>
                <p class="header-subtitle">Kelola data master supplier / pemasok obat apotek</p>
            </div>
            
            <div class="user-nav">
                <span>{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-logout">Keluar</button>
                </form>
            </div>
        </div>

        <!-- Session Success Alert -->
        @if(session('success'))
            <div class="alert-success" id="success-alert">
                <span>{{ session('success') }}</span>
                <button class="alert-close" onclick="document.getElementById('success-alert').remove()">&times;</button>
            </div>
        @endif

        <!-- Session Validation Errors Alert -->
        @if ($errors->any())
            <div class="alert-success" style="background-color: var(--danger-bg); color: var(--danger-text); border-color: rgba(239, 68, 68, 0.2);" id="error-alert">
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <span style="font-weight: 700;">Gagal memproses data:</span>
                    <ul style="margin-left: 16px; font-size: 13px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button class="alert-close" onclick="document.getElementById('error-alert').remove()">&times;</button>
            </div>
        @endif

        <!-- Main Panel -->
        <div class="panel">
            <!-- Filter Bar -->
            <div class="filter-form">
                <div class="filter-left">
                    <!-- Search Input -->
                    <form action="{{ route('suppliers.index') }}" method="GET" style="display: flex; align-items: center; gap: 12px; flex: 1;">
                        <!-- Retain Status Filter -->
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        
                        <div class="search-container">
                            <span class="search-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </span>
                            <input type="text" name="search" class="search-input" placeholder="Cari nama supplier atau kontak..." value="{{ request('search') }}">
                        </div>

                        <!-- Mockup Filter Dropdown -->
                        <div class="filter-dropdown-container">
                            <button type="button" class="btn-filter" id="filter-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                                <span>Filter: {{ request('status') ?: 'Semua Status' }}</span>
                            </button>
                            
                            <div class="filter-dropdown-menu" id="filter-menu">
                                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="filter-dropdown-item {{ !request('status') ? 'active' : '' }}">Semua Status</a>
                                <a href="{{ request()->fullUrlWithQuery(['status' => 'Aktif']) }}" class="filter-dropdown-item {{ request('status') === 'Aktif' ? 'active' : '' }}">Aktif</a>
                                <a href="{{ request()->fullUrlWithQuery(['status' => 'Nonaktif']) }}" class="filter-dropdown-item {{ request('status') === 'Nonaktif' ? 'active' : '' }}">Nonaktif</a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Add Supplier Button -->
                <button class="btn-add" onclick="openCreateModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Tambah Supplier
                </button>
            </div>

            <!-- Table Responsive Container -->
            <div class="table-responsive">
                <table class="supplier-table">
                    <thead>
                        <tr>
                            <th style="width: 250px;">Nama Supplier</th>
                            <th>Kontak Person</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th style="width: 100px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td>
                                <div class="supplier-name-cell">
                                    <span class="supplier-name">{{ $supplier->name }}</span>
                                    <span class="supplier-subtitle">{{ $supplier->city ?: 'Luar Kota' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="supplier-contact">{{ $supplier->contact_person ?: '-' }}</span>
                            </td>
                            <td>
                                <span class="supplier-phone">{{ $supplier->phone ?: '-' }}</span>
                            </td>
                            <td>
                                <div class="supplier-address" title="{{ $supplier->address }}">
                                    {{ $supplier->address ?: '-' }}
                                </div>
                            </td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge-status aktif">Aktif</span>
                                @else
                                    <span class="badge-status nonaktif">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-cell" style="justify-content: center;">
                                    <!-- Edit Icon Button -->
                                    <button class="btn-action edit" title="Edit Supplier"
                                            data-id="{{ $supplier->id }}"
                                            data-name="{{ $supplier->name }}"
                                            data-contact_person="{{ $supplier->contact_person }}"
                                            data-phone="{{ $supplier->phone }}"
                                            data-email="{{ $supplier->email }}"
                                            data-city="{{ $supplier->city }}"
                                            data-address="{{ $supplier->address }}"
                                            data-is_active="{{ $supplier->is_active ? 1 : 0 }}"
                                            onclick="openEditModal(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier {{ addslashes($supplier->name) }}?')" style="margin: 0; display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action delete" title="Hapus Supplier">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="empty-state">Data supplier tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination Area -->
            @if($suppliers->hasPages() || $suppliers->total() > 0)
            <div class="panel-footer">
                <span class="footer-info">
                    Menampilkan {{ $suppliers->firstItem() ?: 0 }} sampai {{ $suppliers->lastItem() ?: 0 }} dari {{ $suppliers->total() }} entri
                </span>
                
                @if($suppliers->hasPages())
                <div class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($suppliers->onFirstPage())
                        <span class="page-link disabled">Sebelumnya</span>
                    @else
                        <a href="{{ $suppliers->previousPageUrl() }}" class="page-link">Sebelumnya</a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($suppliers->links()->elements as $element)
                        @if (is_string($element))
                            <span class="page-link disabled">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $suppliers->currentPage())
                                    <span class="page-link active">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($suppliers->hasMorePages())
                        <a href="{{ $suppliers->nextPageUrl() }}" class="page-link">Selanjutnya</a>
                    @else
                        <span class="page-link disabled">Selanjutnya</span>
                    @endif
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- CREATE SUPPLIER MODAL -->
    <div class="modal-overlay" id="create-modal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Supplier Baru</h3>
                <button class="btn-close-modal" onclick="closeCreateModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="create-name">Nama Supplier <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="name" id="create-name" class="form-input" placeholder="Masukkan nama pemasok/supplier" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="create-contact">Kontak Person</label>
                            <input type="text" name="contact_person" id="create-contact" class="form-input" placeholder="Nama penghubung">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="create-phone">Telepon</label>
                            <input type="text" name="phone" id="create-phone" class="form-input" placeholder="Nomor telepon">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="create-email">Email</label>
                            <input type="email" name="email" id="create-email" class="form-input" placeholder="alamat@domain.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="create-city">Kota</label>
                            <input type="text" name="city" id="create-city" class="form-input" placeholder="Kota lokasi">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="create-address">Alamat Lengkap</label>
                        <textarea name="address" id="create-address" class="form-input form-textarea" placeholder="Alamat lengkap supplier..."></textarea>
                    </div>

                    <div class="form-group form-checkbox-group">
                        <input type="checkbox" name="is_active" id="create-is-active" class="form-checkbox" value="1" checked>
                        <label class="form-label" for="create-is-active" style="cursor: pointer; user-select: none;">Supplier ini Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeCreateModal()">Batal</button>
                    <button type="submit" class="btn-submit">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT SUPPLIER MODAL -->
    <div class="modal-overlay" id="edit-modal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Ubah Data Supplier</h3>
                <button class="btn-close-modal" onclick="closeEditModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form action="" method="POST" id="edit-form">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="edit-name">Nama Supplier <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="name" id="edit-name" class="form-input" placeholder="Masukkan nama pemasok/supplier" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="edit-contact">Kontak Person</label>
                            <input type="text" name="contact_person" id="edit-contact" class="form-input" placeholder="Nama penghubung">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit-phone">Telepon</label>
                            <input type="text" name="phone" id="edit-phone" class="form-input" placeholder="Nomor telepon">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="edit-email">Email</label>
                            <input type="email" name="email" id="edit-email" class="form-input" placeholder="alamat@domain.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit-city">Kota</label>
                            <input type="text" name="city" id="edit-city" class="form-input" placeholder="Kota lokasi">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit-address">Alamat Lengkap</label>
                        <textarea name="address" id="edit-address" class="form-input form-textarea" placeholder="Alamat lengkap supplier..."></textarea>
                    </div>

                    <div class="form-group form-checkbox-group">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="edit-is-active" class="form-checkbox" value="1">
                        <label class="form-label" for="edit-is-active" style="cursor: pointer; user-select: none;">Supplier ini Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn-submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modals & Dropdown Script -->
    <script>
        // Filter dropdown logic
        const filterBtn = document.getElementById('filter-btn');
        const filterMenu = document.getElementById('filter-menu');

        filterBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            filterMenu.classList.toggle('show');
        });

        document.addEventListener('click', () => {
            filterMenu.classList.remove('show');
        });

        // Create Modal handlers
        const createModal = document.getElementById('create-modal');
        function openCreateModal() {
            createModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        function closeCreateModal() {
            createModal.classList.remove('show');
            document.body.style.overflow = '';
        }

        // Edit Modal handlers
        const editModal = document.getElementById('edit-modal');
        const editForm = document.getElementById('edit-form');
        const editName = document.getElementById('edit-name');
        const editContact = document.getElementById('edit-contact');
        const editPhone = document.getElementById('edit-phone');
        const editEmail = document.getElementById('edit-email');
        const editCity = document.getElementById('edit-city');
        const editAddress = document.getElementById('edit-address');
        const editIsActive = document.getElementById('edit-is-active');

        function openEditModal(button) {
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const contactPerson = button.getAttribute('data-contact_person');
            const phone = button.getAttribute('data-phone');
            const email = button.getAttribute('data-email');
            const city = button.getAttribute('data-city');
            const address = button.getAttribute('data-address');
            const isActive = button.getAttribute('data-is_active');

            // Populate form values
            editName.value = name || '';
            editContact.value = contactPerson || '';
            editPhone.value = phone || '';
            editEmail.value = email || '';
            editCity.value = city || '';
            editAddress.value = address || '';
            
            // Handle checkbox status
            editIsActive.checked = isActive == '1';

            // Set action URL dynamically
            editForm.action = `/suppliers/${id}`;

            // Show modal
            editModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            editModal.classList.remove('show');
            document.body.style.overflow = '';
        }

        // Close modal when clicking outside the box
        window.addEventListener('click', (e) => {
            if (e.target === createModal) {
                closeCreateModal();
            }
            if (e.target === editModal) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>
