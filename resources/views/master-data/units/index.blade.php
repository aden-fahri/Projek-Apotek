@extends('layouts.admin')

@section('title', 'Satuan Obat')

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
    .stat-icon.orange { background: rgba(249,115,22,0.12); color: #EA580C; }
    .stat-icon.green  { background: rgba(16,185,129,0.12); color: #059669; }
    .stat-icon.blue   { background: rgba(59,130,246,0.12); color: #3B82F6; }
    .stat-label { font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.04em; }
    .stat-value { font-size: 26px; font-weight: 800; color: var(--text-primary); margin-top: 2px; }
    .badge-abbr {
        font-size: 12px; font-weight: 800; padding: 4px 10px; border-radius: 8px;
        background: rgba(249,115,22,0.1); color: #EA580C; letter-spacing: 0.04em;
    }
    .badge-count { font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 9999px; background: rgba(13,148,136,0.12); color: #0D9488; }
    @media (max-width: 768px) { .stats-row { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="master-container">

    <div class="header-section">
        <div class="header-title-area">
            <h1 class="header-title">Satuan Obat</h1>
            <p class="header-subtitle">Kelola satuan pengukuran obat (tablet, kapsul, ml, botol, dll.)</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fa-solid fa-ruler"></i></div>
            <div>
                <div class="stat-label">Total Satuan</div>
                <div class="stat-value">{{ $units->total() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fa-solid fa-capsules"></i></div>
            <div>
                <div class="stat-label">Satuan Terpakai</div>
                <div class="stat-value">{{ $units->getCollection()->filter(fn($u) => $u->medicines_count > 0)->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fa-solid fa-font"></i></div>
            <div>
                <div class="stat-label">Ada Singkatan</div>
                <div class="stat-value">{{ $units->getCollection()->filter(fn($u) => !empty($u->abbreviation))->count() }}</div>
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
                <form action="{{ route('units.index') }}" method="GET" style="display:flex;align-items:center;gap:12px;flex:1;">
                    <div class="search-container">
                        <span class="search-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </span>
                        <input type="text" name="search" class="search-input" placeholder="Cari nama atau singkatan satuan..." value="{{ request('search') }}">
                    </div>
                </form>
            </div>
            <button class="btn-add" onclick="openCreateModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Satuan
            </button>
        </div>

        <div class="table-responsive">
            <table class="supplier-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Satuan</th>
                        <th style="width:130px;">Singkatan</th>
                        <th style="text-align:center;">Jumlah Obat</th>
                        <th style="width:100px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $i => $unit)
                    <tr>
                        <td style="color:var(--text-secondary);font-weight:600;">{{ $units->firstItem() + $i }}</td>
                        <td><span class="supplier-name">{{ $unit->name }}</span></td>
                        <td>
                            @if($unit->abbreviation)
                                <span class="badge-abbr">{{ $unit->abbreviation }}</span>
                            @else
                                <span style="color:var(--text-secondary);">—</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <span class="badge-count">{{ $unit->medicines_count }}</span>
                        </td>
                        <td>
                            <div class="action-cell" style="justify-content:center;">
                                <button class="btn-action edit" title="Edit Satuan"
                                    data-id="{{ $unit->id }}"
                                    data-name="{{ $unit->name }}"
                                    data-abbreviation="{{ $unit->abbreviation }}"
                                    onclick="openEditModal(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </button>
                                @if($unit->medicines_count == 0)
                                <form action="{{ route('units.destroy', $unit->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus satuan {{ addslashes($unit->name) }}?')"
                                    style="margin:0;display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete" title="Hapus Satuan">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>
                                </form>
                                @else
                                <button class="btn-action" title="Tidak bisa dihapus (masih digunakan)" style="opacity:.3;cursor:not-allowed;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="empty-state">Belum ada data satuan obat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($units->hasPages() || $units->total() > 0)
        <div class="panel-footer">
            <span class="footer-info">Menampilkan {{ $units->firstItem() ?: 0 }}–{{ $units->lastItem() ?: 0 }} dari {{ $units->total() }} entri</span>
            @if($units->hasPages())
            <div class="pagination">
                @if($units->onFirstPage())
                    <span class="page-link disabled">Sebelumnya</span>
                @else
                    <a href="{{ $units->previousPageUrl() }}" class="page-link">Sebelumnya</a>
                @endif
                @foreach($units->links()->elements as $element)
                    @if(is_string($element))<span class="page-link disabled">{{ $element }}</span>@endif
                    @if(is_array($element))
                        @foreach($element as $page => $url)
                            @if($page == $units->currentPage())
                                <span class="page-link active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
                @if($units->hasMorePages())
                    <a href="{{ $units->nextPageUrl() }}" class="page-link">Selanjutnya</a>
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
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Satuan Obat</h3>
            <button class="btn-close-modal" onclick="closeCreateModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <form action="{{ route('units.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="create-name">Nama Satuan <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" id="create-name" class="form-input" placeholder="Contoh: Tablet, Kapsul, Botol, Ml..." required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="create-abbreviation">Singkatan</label>
                    <input type="text" name="abbreviation" id="create-abbreviation" class="form-input" placeholder="Contoh: tab, kap, ml..." maxlength="20">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeCreateModal()">Batal</button>
                <button type="submit" class="btn-submit">Simpan Satuan</button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal-overlay" id="edit-modal">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <h3 class="modal-title">Edit Satuan Obat</h3>
            <button class="btn-close-modal" onclick="closeEditModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <form action="" method="POST" id="edit-form">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="edit-name">Nama Satuan <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" id="edit-name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit-abbreviation">Singkatan</label>
                    <input type="text" name="abbreviation" id="edit-abbreviation" class="form-input" maxlength="20">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const createModal = document.getElementById('create-modal');
    const editModal   = document.getElementById('edit-modal');
    const editForm    = document.getElementById('edit-form');

    function openCreateModal()  { createModal.classList.add('show'); document.body.style.overflow = 'hidden'; }
    function closeCreateModal() { createModal.classList.remove('show'); document.body.style.overflow = ''; }
    function openEditModal(btn) {
        document.getElementById('edit-name').value         = btn.dataset.name || '';
        document.getElementById('edit-abbreviation').value = btn.dataset.abbreviation || '';
        editForm.action = `/units/${btn.dataset.id}`;
        editModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeEditModal() { editModal.classList.remove('show'); document.body.style.overflow = ''; }

    window.addEventListener('click', e => {
        if (e.target === createModal) closeCreateModal();
        if (e.target === editModal)   closeEditModal();
    });
    setTimeout(() => {
        ['flash-alert','flash-err'].forEach(id => {
            const el = document.getElementById(id);
            if (el) { el.style.transition='opacity .4s'; el.style.opacity=0; setTimeout(()=>el.remove(), 400); }
        });
    }, 4000);
</script>
@endpush
