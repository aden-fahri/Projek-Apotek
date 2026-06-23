@extends('layouts.admin')

@section('title', 'Kategori Obat')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/suppliers.css') }}">
<style>
    .master-container { width: 100%; display: flex; flex-direction: column; gap: 24px; }
    .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .stat-card {
        background: var(--white); border-radius: 14px; padding: 20px 24px;
        border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);
        display: flex; align-items: center; gap: 16px;
    }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
    .stat-icon.teal  { background: rgba(13,148,136,0.12); color: #0D9488; }
    .stat-icon.blue  { background: rgba(59,130,246,0.12); color: #3B82F6; }
    .stat-icon.amber { background: rgba(245,158,11,0.12); color: #D97706; }
    .stat-label { font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.04em; }
    .stat-value { font-size: 26px; font-weight: 800; color: var(--text-primary); margin-top: 2px; }
    .badge-count {
        font-size: 11px; font-weight: 700; padding: 3px 8px;
        border-radius: 9999px; background: rgba(13,148,136,0.12); color: #0D9488;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .badge-clickable { cursor: pointer; transition: transform .12s ease, box-shadow .12s ease; }
    .badge-clickable:hover { transform: scale(1.08); box-shadow: 0 2px 8px rgba(13,148,136,.25); }
    @media (max-width: 768px) { .stats-row { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="master-container">

    <div class="header-section">
        <div class="header-title-area">
            <h1 class="header-title">Kategori Obat</h1>
            <p class="header-subtitle">Kelola kategori pengelompokan jenis obat di apotek</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fa-solid fa-tags"></i></div>
            <div>
                <div class="stat-label">Total Kategori</div>
                <div class="stat-value">{{ $categories->total() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fa-solid fa-capsules"></i></div>
            <div>
                <div class="stat-label">Kategori Terpakai</div>
                <div class="stat-value">{{ $categories->getCollection()->filter(fn($c) => $c->medicines_count > 0)->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber"><i class="fa-solid fa-circle-exclamation"></i></div>
            <div>
                <div class="stat-label">Kategori Kosong</div>
                <div class="stat-value">{{ $categories->getCollection()->filter(fn($c) => $c->medicines_count === 0)->count() }}</div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert-success" id="flash-alert">
            <span>{{ session('success') }}</span>
            <button class="alert-close" onclick="document.getElementById('flash-alert').remove()">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert-success" style="background:#FEE2E2;color:#B91C1C;border-color:rgba(239,68,68,.2);" id="flash-err">
            <span>{{ session('error') }}</span>
            <button class="alert-close" onclick="document.getElementById('flash-err').remove()">&times;</button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert-success" style="background:#FEE2E2;color:#B91C1C;border-color:rgba(239,68,68,.2);" id="val-errors">
            <div style="display:flex;flex-direction:column;gap:4px;">
                <span style="font-weight:700;">Gagal menyimpan data:</span>
                <ul style="margin-left:16px;font-size:13px;">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            <button class="alert-close" onclick="document.getElementById('val-errors').remove()">&times;</button>
        </div>
    @endif

    {{-- Main Panel --}}
    <div class="panel">
        <div class="filter-form">
            <div class="filter-left">
                <form action="{{ route('categories.index') }}" method="GET" style="display:flex;align-items:center;gap:12px;flex:1;">
                    <div class="search-container">
                        <span class="search-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </span>
                        <input type="text" name="search" class="search-input" placeholder="Cari nama atau deskripsi kategori..." value="{{ request('search') }}">
                    </div>
                </form>
            </div>
            <button class="btn-add" onclick="openCreateModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Kategori
            </button>
        </div>

        <div class="table-responsive">
            <table class="supplier-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th style="text-align:center;">Jumlah Obat</th>
                        <th style="width:110px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $i => $category)
                    <tr>
                        <td style="color:var(--text-secondary);font-weight:600;">{{ $categories->firstItem() + $i }}</td>
                        <td>
                            <span class="supplier-name">{{ $category->name }}</span>
                        </td>
                        <td>
                            <div class="supplier-address" title="{{ $category->description }}" style="max-width:300px;">
                                {{ $category->description ?: '-' }}
                            </div>
                        </td>
                        <td style="text-align:center;">
                            @if($category->medicines_count > 0)
                                <button type="button" class="badge-count badge-clickable"
                                    title="Lihat daftar obat"
                                    onclick="openMedicinesModal('{{ route('categories.medicines', $category->id) }}', '{{ addslashes($category->name) }}', {{ $category->medicines_count }})">
                                    <i class="fa-solid fa-capsules" style="font-size:10px;"></i>
                                    {{ $category->medicines_count }} obat
                                </button>
                            @else
                                <span style="color:var(--text-secondary);font-size:13px;">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-cell" style="justify-content:center;">
                                <button class="btn-action edit" title="Edit Kategori"
                                    data-id="{{ $category->id }}"
                                    data-name="{{ $category->name }}"
                                    data-description="{{ $category->description }}"
                                    onclick="openEditModal(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </button>
                                <button class="btn-action delete" title="Hapus Kategori"
                                    onclick="confirmDelete(
                                        '{{ route('categories.destroy', $category->id) }}',
                                        '{{ addslashes($category->name) }}',
                                        {{ $category->medicines_count }}
                                    )">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="empty-state">Belum ada data kategori obat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages() || $categories->total() > 0)
        <div class="panel-footer">
            <span class="footer-info">Menampilkan {{ $categories->firstItem() ?: 0 }}–{{ $categories->lastItem() ?: 0 }} dari {{ $categories->total() }} entri</span>
            @if($categories->hasPages())
            <div class="pagination">
                @if($categories->onFirstPage())
                    <span class="page-link disabled">Sebelumnya</span>
                @else
                    <a href="{{ $categories->previousPageUrl() }}" class="page-link">Sebelumnya</a>
                @endif
                @foreach($categories->links()->elements as $element)
                    @if(is_string($element))<span class="page-link disabled">{{ $element }}</span>@endif
                    @if(is_array($element))
                        @foreach($element as $page => $url)
                            @if($page == $categories->currentPage())
                                <span class="page-link active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
                @if($categories->hasMorePages())
                    <a href="{{ $categories->nextPageUrl() }}" class="page-link">Selanjutnya</a>
                @else
                    <span class="page-link disabled">Selanjutnya</span>
                @endif
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- CREATE MODAL --}}
<div class="modal-overlay" id="create-modal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Kategori Obat</h3>
            <button class="btn-close-modal" onclick="closeCreateModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="create-name">Nama Kategori <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" id="create-name" class="form-input" placeholder="Contoh: Antibiotik, Vitamin &amp; Suplemen..." required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="create-description">Deskripsi</label>
                    <textarea name="description" id="create-description" class="form-input form-textarea" placeholder="Deskripsi singkat kategori obat..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeCreateModal()">Batal</button>
                <button type="submit" class="btn-submit">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal-overlay" id="edit-modal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Edit Kategori Obat</h3>
            <button class="btn-close-modal" onclick="closeEditModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <form action="" method="POST" id="edit-form">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="edit-name">Nama Kategori <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" id="edit-name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit-description">Deskripsi</label>
                    <textarea name="description" id="edit-description" class="form-input form-textarea"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- DELETE CONFIRM MODAL --}}
<div class="modal-overlay" id="delete-modal">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header" style="border-bottom:none;padding-bottom:0;">
            <div style="width:48px;height:48px;background:#FEE2E2;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
            </div>
        </div>
        <div class="modal-body" style="text-align:center;padding-top:16px;">
            <h3 id="delete-title" style="font-size:17px;font-weight:800;color:var(--text-primary);margin-bottom:8px;">Hapus Kategori?</h3>
            <p id="delete-message" style="font-size:14px;color:var(--text-secondary);line-height:1.6;"></p>
        </div>
        <div class="modal-footer" style="justify-content:center;gap:12px;">
            <button class="btn-secondary" onclick="closeDeleteModal()" style="min-width:100px;">Batal</button>
            <form id="delete-form" action="" method="POST" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" id="delete-confirm-btn" class="btn-submit" style="background:linear-gradient(135deg,#EF4444,#DC2626);min-width:100px;">Hapus</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('master-data.partials.medicines-modal')
<script>
    const createModal = document.getElementById('create-modal');
    const editModal   = document.getElementById('edit-modal');
    const deleteModal = document.getElementById('delete-modal');
    const editForm    = document.getElementById('edit-form');
    const deleteForm  = document.getElementById('delete-form');

    function openCreateModal()  { createModal.classList.add('show'); document.body.style.overflow = 'hidden'; }
    function closeCreateModal() { createModal.classList.remove('show'); document.body.style.overflow = ''; }

    function openEditModal(btn) {
        document.getElementById('edit-name').value        = btn.dataset.name || '';
        document.getElementById('edit-description').value = btn.dataset.description || '';
        editForm.action = '/categories/' + btn.dataset.id;
        editModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeEditModal() { editModal.classList.remove('show'); document.body.style.overflow = ''; }

    function confirmDelete(url, name, count) {
        const title   = document.getElementById('delete-title');
        const message = document.getElementById('delete-message');
        const btn     = document.getElementById('delete-confirm-btn');
        deleteForm.action = url;

        if (count > 0) {
            title.textContent   = 'Tidak Dapat Dihapus';
            message.innerHTML   = 'Kategori <strong>' + name + '</strong> masih digunakan oleh <strong>' + count + ' obat</strong>.<br>Hapus atau pindahkan obat-obat tersebut terlebih dahulu sebelum menghapus kategori ini.';
            btn.style.display   = 'none';
        } else {
            title.textContent   = 'Hapus Kategori?';
            message.innerHTML   = 'Anda yakin ingin menghapus kategori <strong>' + name + '</strong>?<br><span style="color:#EF4444;font-size:12px;">Tindakan ini tidak dapat dibatalkan.</span>';
            btn.style.display   = '';
        }
        deleteModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeDeleteModal() { deleteModal.classList.remove('show'); document.body.style.overflow = ''; }

    window.addEventListener('click', e => {
        if (e.target === createModal) closeCreateModal();
        if (e.target === editModal)   closeEditModal();
        if (e.target === deleteModal) closeDeleteModal();
    });

    setTimeout(() => {
        ['flash-alert','flash-err'].forEach(id => {
            const el = document.getElementById(id);
            if (el) { el.style.transition='opacity .4s'; el.style.opacity=0; setTimeout(()=>el.remove(), 400); }
        });
    }, 4000);
</script>
@endpush
