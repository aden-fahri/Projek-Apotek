@php $footerSetting = \App\Models\PharmacySetting::getSetting(); @endphp

<footer style="
    border-top: 1px solid #e5e7eb;
    background: #fafafa;
    padding: 12px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
">
    {{-- Kiri: Nama Apotek, SIA, Apoteker --}}
    <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
        <div style="display:flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-hospital" style="color:#0D9488; font-size:13px;"></i>
            <span style="font-size:12px; font-weight:700; color:#1E293B;">{{ $footerSetting->pharmacy_name }}</span>
            @if($footerSetting->license_number)
            <span style="font-size:11px; color:#64748B; background:#f1f5f9; padding:2px 8px; border-radius:4px;">
                SIA: {{ $footerSetting->license_number }}
            </span>
            @endif
        </div>
        @if($footerSetting->pharmacist_name)
        <div style="font-size:11px; color:#64748B; display:flex; align-items:center; gap:4px;">
            <i class="fa-solid fa-user-nurse" style="font-size:11px;"></i>
            {{ $footerSetting->pharmacist_name }}
            @if($footerSetting->pharmacist_license)
            &nbsp;·&nbsp; {{ $footerSetting->pharmacist_license }}
            @endif
        </div>
        @endif
    </div>

    {{-- Kanan: Alamat, Telepon, Email --}}
    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
        @if($footerSetting->address)
        <span style="font-size:11px; color:#64748B; display:flex; align-items:center; gap:4px;">
            <i class="fa-solid fa-location-dot" style="color:#9ca3af;"></i>
            {{ Str::limit($footerSetting->address, 45) }}
        </span>
        @endif
        @if($footerSetting->phone)
        <span style="font-size:11px; color:#64748B; display:flex; align-items:center; gap:4px;">
            <i class="fa-solid fa-phone" style="color:#9ca3af;"></i>
            {{ $footerSetting->phone }}
        </span>
        @endif
        @if($footerSetting->email)
        <span style="font-size:11px; color:#64748B; display:flex; align-items:center; gap:4px;">
            <i class="fa-solid fa-envelope" style="color:#9ca3af;"></i>
            {{ $footerSetting->email }}
        </span>
        @endif
    </div>
</footer>
