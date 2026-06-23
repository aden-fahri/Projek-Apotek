{{--
    Shared "Daftar Obat" modal — include di halaman kategori, golongan, satuan.
    Memerlukan: data-url="..." pada tiap badge/trigger.
--}}

{{-- ===== MODAL DAFTAR OBAT ===== --}}
<div class="modal-overlay" id="medicines-list-modal">
    <div class="modal-box" style="max-width:680px;">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:34px;height:34px;background:rgba(13,148,136,0.12);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#0D9488;">
                    <i class="fa-solid fa-capsules" style="font-size:15px;"></i>
                </div>
                <div>
                    <h3 class="modal-title" id="medicines-modal-title" style="font-size:16px;margin:0;">Daftar Obat</h3>
                    <p style="font-size:12px;color:var(--text-secondary);margin:0;" id="medicines-modal-subtitle"></p>
                </div>
            </div>
            <button class="btn-close-modal" onclick="closeMedicinesModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        {{-- Search inside modal --}}
        <div style="padding:14px 20px;border-bottom:1px solid var(--border-color);background:#FAFAF9;">
            <div class="search-container" style="max-width:100%;">
                <span class="search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="medicines-modal-search" class="search-input" placeholder="Cari nama atau kode obat..." style="font-size:13px;padding:9px 12px 9px 38px;"
                    oninput="filterMedicinesModal(this.value)">
            </div>
        </div>

        {{-- Table body --}}
        <div class="modal-body" style="padding:0;max-height:420px;overflow-y:auto;">
            {{-- Loading state --}}
            <div id="medicines-loading" style="padding:40px;text-align:center;color:var(--text-secondary);">
                <i class="fa-solid fa-circle-notch fa-spin" style="font-size:24px;margin-bottom:10px;display:block;color:#0D9488;"></i>
                <span style="font-size:14px;font-weight:600;">Memuat data...</span>
            </div>

            {{-- Empty state --}}
            <div id="medicines-empty" style="display:none;padding:50px 20px;text-align:center;color:var(--text-secondary);">
                <i class="fa-solid fa-box-open" style="font-size:32px;margin-bottom:10px;display:block;opacity:.4;"></i>
                <span style="font-size:14px;font-weight:600;">Tidak ada obat di grup ini.</span>
            </div>

            {{-- Table --}}
            <div id="medicines-table-wrap" style="display:none;">
                <table id="medicines-modal-table" style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#FAF8F4;position:sticky;top:0;z-index:1;">
                            <th style="padding:11px 16px;font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid var(--border-color);white-space:nowrap;">Kode</th>
                            <th style="padding:11px 16px;font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid var(--border-color);">Nama Obat</th>
                            <th style="padding:11px 16px;font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid var(--border-color);text-align:right;white-space:nowrap;">Harga Jual</th>
                            <th style="padding:11px 16px;font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid var(--border-color);text-align:center;white-space:nowrap;">Stok</th>
                            <th style="padding:11px 16px;font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid var(--border-color);text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="medicines-modal-tbody"></tbody>
                </table>
            </div>
        </div>

        <div class="modal-footer" style="justify-content:space-between;">
            <span style="font-size:13px;font-weight:600;color:var(--text-secondary);" id="medicines-count-label"></span>
            <button class="btn-secondary" onclick="closeMedicinesModal()">Tutup</button>
        </div>
    </div>
</div>

<style>
    #medicines-modal-tbody tr { transition: background .12s ease; }
    #medicines-modal-tbody tr:hover { background: #FDFDFB; }
    #medicines-modal-tbody td { padding: 13px 16px; font-size: 13px; color: var(--text-primary); border-bottom: 1px solid var(--border-color); vertical-align: middle; }
    .medicine-pill-active   { font-size:11px;font-weight:700;padding:3px 9px;border-radius:9999px;background:#DEF7EC;color:#03543F; }
    .medicine-pill-inactive { font-size:11px;font-weight:700;padding:3px 9px;border-radius:9999px;background:#F3F4F6;color:#4B5563; }
    .medicine-pill-rx       { font-size:11px;font-weight:700;padding:3px 7px;border-radius:6px;background:rgba(239,68,68,.1);color:#B91C1C;margin-left:4px; }
    .stock-badge-ok   { font-size:11px;font-weight:700;padding:3px 9px;border-radius:9999px;background:rgba(13,148,136,.12);color:#0D9488; }
    .stock-badge-low  { font-size:11px;font-weight:700;padding:3px 9px;border-radius:9999px;background:rgba(245,158,11,.12);color:#D97706; }
    .stock-badge-zero { font-size:11px;font-weight:700;padding:3px 9px;border-radius:9999px;background:#FEE2E2;color:#B91C1C; }
    .badge-clickable {
        cursor: pointer;
        transition: transform .12s ease, box-shadow .12s ease;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .badge-clickable:hover { transform: scale(1.08); box-shadow: 0 2px 8px rgba(13,148,136,.25); }
</style>

<script>
    let _allMedicines = [];

    function openMedicinesModal(url, groupName, count) {
        const modal = document.getElementById('medicines-list-modal');
        document.getElementById('medicines-modal-title').textContent = 'Daftar Obat — ' + groupName;
        document.getElementById('medicines-modal-subtitle').textContent = count + ' obat terdaftar';
        document.getElementById('medicines-loading').style.display   = 'block';
        document.getElementById('medicines-empty').style.display     = 'none';
        document.getElementById('medicines-table-wrap').style.display = 'none';
        document.getElementById('medicines-modal-search').value      = '';
        document.getElementById('medicines-count-label').textContent = '';
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                _allMedicines = data.medicines || [];
                renderMedicinesTable(_allMedicines);
            })
            .catch(() => {
                document.getElementById('medicines-loading').style.display = 'none';
                document.getElementById('medicines-empty').style.display   = 'block';
                document.getElementById('medicines-empty').innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="font-size:28px;margin-bottom:10px;display:block;color:#EF4444;"></i><span style="font-size:14px;font-weight:600;color:#B91C1C;">Gagal memuat data obat.</span>';
            });
    }

    function renderMedicinesTable(list) {
        document.getElementById('medicines-loading').style.display = 'none';
        const tbody = document.getElementById('medicines-modal-tbody');
        tbody.innerHTML = '';

        if (!list.length) {
            document.getElementById('medicines-empty').style.display     = 'block';
            document.getElementById('medicines-table-wrap').style.display = 'none';
            document.getElementById('medicines-count-label').textContent = 'Tidak ada data';
            return;
        }

        document.getElementById('medicines-empty').style.display      = 'none';
        document.getElementById('medicines-table-wrap').style.display = 'block';
        document.getElementById('medicines-count-label').textContent   = list.length + ' obat ditampilkan';

        list.forEach(m => {
            const priceFormatted = 'Rp ' + Number(m.selling_price).toLocaleString('id-ID');
            const stockClass = m.current_stock === 0 ? 'stock-badge-zero' : m.current_stock < 10 ? 'stock-badge-low' : 'stock-badge-ok';
            const rxBadge = m.requires_prescription ? '<span class="medicine-pill-rx">Rx</span>' : '';
            const activeBadge = m.is_active
                ? '<span class="medicine-pill-active">Aktif</span>'
                : '<span class="medicine-pill-inactive">Nonaktif</span>';

            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td><code style="font-size:11px;background:#F1F5F9;padding:2px 7px;border-radius:5px;font-weight:700;color:#475569;">${m.code || '—'}</code></td>
                    <td>
                        <div style="font-weight:700;font-size:13px;color:var(--text-primary);">${m.name}</div>
                        <div style="font-size:11px;color:var(--text-secondary);margin-top:1px;">${m.unit} ${rxBadge}</div>
                    </td>
                    <td style="text-align:right;font-weight:700;color:#0D9488;white-space:nowrap;">${priceFormatted}</td>
                    <td style="text-align:center;"><span class="${stockClass}">${m.current_stock} ${m.unit}</span></td>
                    <td style="text-align:center;">${activeBadge}</td>
                </tr>
            `);
        });
    }

    function filterMedicinesModal(q) {
        const term = q.toLowerCase().trim();
        const filtered = term ? _allMedicines.filter(m =>
            m.name.toLowerCase().includes(term) || (m.code || '').toLowerCase().includes(term)
        ) : _allMedicines;
        renderMedicinesTable(filtered);
    }

    function closeMedicinesModal() {
        const modal = document.getElementById('medicines-list-modal');
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }

    // Close on backdrop click
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('medicines-list-modal').addEventListener('click', e => {
            if (e.target === e.currentTarget) closeMedicinesModal();
        });
    });
</script>
