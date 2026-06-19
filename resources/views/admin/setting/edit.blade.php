@extends('layouts.admin')

@section('title', 'Pengaturan Apotek')

@push('styles')
<style>
    .setting-container {
        width: 100%;
    }
    .setting-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        padding: 28px 32px;
        margin-bottom: 24px;
    }
    .setting-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #0F766E;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f0fdf4;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .setting-card-title i {
        font-size: 16px;
    }
    .form-group {
        margin-bottom: 18px;
    }
    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }
    .form-group label span.required {
        color: #EF4444;
        margin-left: 2px;
    }
    .form-control {
        width: 100%;
        padding: 9px 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Quicksand', sans-serif;
        color: #1E293B;
        background: #fafafa;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }
    .form-control:focus {
        outline: none;
        border-color: #0D9488;
        box-shadow: 0 0 0 3px rgba(13,148,136,0.1);
        background: #fff;
    }
    .form-control.is-invalid {
        border-color: #EF4444;
    }
    .invalid-feedback {
        color: #EF4444;
        font-size: 12px;
        margin-top: 4px;
    }
    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }
    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .logo-preview-wrap {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 12px;
    }
    .logo-preview-img {
        width: 90px;
        height: 90px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid #e5e7eb;
        background: #f5f5f5;
    }
    .logo-no-image {
        width: 90px;
        height: 90px;
        border-radius: 10px;
        border: 2px dashed #d1d5db;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 12px;
        gap: 6px;
        background: #f9fafb;
    }
    .logo-no-image i {
        font-size: 22px;
    }
    .logo-hint {
        font-size: 12px;
        color: #6b7280;
        margin-top: 6px;
    }
    .btn-save {
        background: #0D9488;
        color: #fff;
        border: none;
        padding: 11px 28px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        font-family: 'Quicksand', sans-serif;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s, transform 0.1s;
    }
    .btn-save:hover {
        background: #0F766E;
        transform: translateY(-1px);
    }
    .btn-save:active {
        transform: translateY(0);
    }
    .alert-success {
        background: #d1fae5;
        border: 1px solid #6ee7b7;
        border-radius: 8px;
        padding: 12px 16px;
        color: #065f46;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }
    .page-header {
        margin-bottom: 28px;
    }
    .page-header h1 {
        font-size: 22px;
        font-weight: 700;
        color: #1E293B;
        margin: 0 0 4px 0;
    }
    .page-header p {
        font-size: 13px;
        color: #64748B;
        margin: 0;
    }
    .form-file-input {
        width: 100%;
        padding: 8px 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        font-family: 'Quicksand', sans-serif;
        background: #fafafa;
        box-sizing: border-box;
        cursor: pointer;
    }
    .form-file-input:focus {
        outline: none;
        border-color: #0D9488;
    }
</style>
@endpush

@section('content')
<div class="setting-container">

    {{-- Page Header --}}
    <div class="page-header">
        <h1><i class="fa-solid fa-gear" style="color:#0D9488; margin-right:8px;"></i>Pengaturan Apotek</h1>
        <p>Kelola informasi identitas apotek, data apoteker, dan preferensi sistem.</p>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Info Card: Preview Data Tersimpan --}}
    <div style="background: linear-gradient(135deg, #0F766E 0%, #0D9488 100%); border-radius: 12px; padding: 24px 28px; margin-bottom: 28px; color: white; display: flex; gap: 40px; flex-wrap: wrap;">
        <div>
            <p style="font-size:11px; opacity:0.75; margin:0 0 4px 0; text-transform:uppercase; letter-spacing:0.5px;">Nama Apotek</p>
            <p style="font-size:18px; font-weight:700; margin:0;">{{ $setting->pharmacy_name }}</p>
            @if($setting->license_number)
            <p style="font-size:12px; opacity:0.85; margin:4px 0 0 0;">SIA: {{ $setting->license_number }}</p>
            @endif
        </div>
        <div>
            <p style="font-size:11px; opacity:0.75; margin:0 0 4px 0; text-transform:uppercase; letter-spacing:0.5px;">Kontak</p>
            @if($setting->phone)
            <p style="font-size:13px; margin:0 0 2px 0;">{{ $setting->phone }}</p>
            @endif
            @if($setting->email)
            <p style="font-size:13px; margin:0 0 2px 0;">{{ $setting->email }}</p>
            @endif
            @if($setting->address)
            <p style="font-size:12px; opacity:0.85; margin:2px 0 0 0;">{{ Str::limit($setting->address, 50) }}</p>
            @endif
        </div>
        <div>
            <p style="font-size:11px; opacity:0.75; margin:0 0 4px 0; text-transform:uppercase; letter-spacing:0.5px;">Apoteker</p>
            @if($setting->pharmacist_name)
            <p style="font-size:13px; margin:0 0 2px 0;">{{ $setting->pharmacist_name }}</p>
            @endif
            @if($setting->pharmacist_license)
            <p style="font-size:12px; opacity:0.85; margin:0;">SIPA: {{ $setting->pharmacist_license }}</p>
            @endif
        </div>
    </div>

    <form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- === CARD 1: Identitas Apotek === --}}
        <div class="setting-card">
            <div class="setting-card-title">
                <i class="fa-solid fa-hospital"></i>
                Identitas Apotek
            </div>
            <div class="form-group">
                <label for="pharmacy_name">Nama Apotek <span class="required">*</span></label>
                <input type="text" id="pharmacy_name" name="pharmacy_name"
                    class="form-control @error('pharmacy_name') is-invalid @enderror"
                    value="{{ old('pharmacy_name', $setting->pharmacy_name) }}"
                    placeholder="Contoh: Apotek Sehat Farma"
                    required>
                @error('pharmacy_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="license_number">Nomor Izin Apotek (SIA)</label>
                <input type="text" id="license_number" name="license_number"
                    class="form-control @error('license_number') is-invalid @enderror"
                    value="{{ old('license_number', $setting->license_number) }}"
                    placeholder="Contoh: SIA-1234/2024/DINKES">
                @error('license_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- === CARD 2: Kontak === --}}
        <div class="setting-card">
            <div class="setting-card-title">
                <i class="fa-solid fa-address-card"></i>
                Informasi Kontak
            </div>
            <div class="form-group">
                <label for="address">Alamat Lengkap</label>
                <textarea id="address" name="address"
                    class="form-control @error('address') is-invalid @enderror"
                    placeholder="Jl. Kesehatan No. 123, Kelurahan, Kecamatan, Kota, Kode Pos"
                    rows="3">{{ old('address', $setting->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="phone">Nomor Telepon</label>
                    <input type="text" id="phone" name="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone', $setting->phone) }}"
                        placeholder="Contoh: 022-1234567">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $setting->email) }}"
                        placeholder="Contoh: info@apotek.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- === CARD 3: Data Apoteker === --}}
        <div class="setting-card">
            <div class="setting-card-title">
                <i class="fa-solid fa-user-nurse"></i>
                Data Apoteker Penanggung Jawab
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="pharmacist_name">Nama Apoteker</label>
                    <input type="text" id="pharmacist_name" name="pharmacist_name"
                        class="form-control @error('pharmacist_name') is-invalid @enderror"
                        value="{{ old('pharmacist_name', $setting->pharmacist_name) }}"
                        placeholder="Contoh: Apt. Dr. Farida, S.Farm">
                    @error('pharmacist_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="pharmacist_license">Nomor SIPA</label>
                    <input type="text" id="pharmacist_license" name="pharmacist_license"
                        class="form-control @error('pharmacist_license') is-invalid @enderror"
                        value="{{ old('pharmacist_license', $setting->pharmacist_license) }}"
                        placeholder="Contoh: SIPA-5678/2024">
                    @error('pharmacist_license')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- === CARD 4: Logo Apotek === --}}
        <div class="setting-card">
            <div class="setting-card-title">
                <i class="fa-solid fa-image"></i>
                Logo Apotek
            </div>
            <div class="logo-preview-wrap">
                @if($setting->logo && file_exists(storage_path('app/public/' . $setting->logo)))
                    <img src="{{ Storage::url($setting->logo) }}" alt="Logo Apotek" class="logo-preview-img" id="logo-preview">
                @else
                    <div class="logo-no-image" id="logo-placeholder">
                        <i class="fa-solid fa-image"></i>
                        <span>Belum ada logo</span>
                    </div>
                    <img src="" alt="Preview Logo" class="logo-preview-img" id="logo-preview" style="display:none;">
                @endif
                <div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="logo">Upload Logo Baru</label>
                        <input type="file" id="logo" name="logo"
                            class="form-file-input @error('logo') is-invalid @enderror"
                            accept="image/jpeg,image/png,image/jpg">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <p class="logo-hint">Format: JPG, JPEG, PNG. Maks. 2MB. Disarankan ukuran 200×200px.</p>
                </div>
            </div>
        </div>

        {{-- === CARD 5: Catatan Kaki === --}}
        <div class="setting-card">
            <div class="setting-card-title">
                <i class="fa-solid fa-file-lines"></i>
                Catatan Kaki Laporan / Struk
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="footer_note">Teks Catatan Kaki</label>
                <textarea id="footer_note" name="footer_note"
                    class="form-control @error('footer_note') is-invalid @enderror"
                    rows="3"
                    placeholder="Contoh: Terima kasih telah berbelanja di apotek kami. Semoga lekas sembuh!">{{ old('footer_note', $setting->footer_note) }}</textarea>
                @error('footer_note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <p class="logo-hint" style="margin-top:6px;">Teks ini akan muncul di bagian bawah struk transaksi dan laporan PDF.</p>
            </div>
        </div>

        {{-- Tombol Simpan --}}
        <div style="display:flex; justify-content: flex-end; padding-bottom: 32px;">
            <button type="submit" class="btn-save" id="btn-simpan">
                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    // Preview logo sebelum upload
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(evt) {
            const preview = document.getElementById('logo-preview');
            const placeholder = document.getElementById('logo-placeholder');
            preview.src = evt.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
