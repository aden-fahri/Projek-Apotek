<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PharmacySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Tampilkan form edit pengaturan apotek.
     */
    public function edit()
    {
        $setting = PharmacySetting::getSetting();
        return view('admin.setting.edit', compact('setting'));
    }

    /**
     * Simpan perubahan pengaturan apotek.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'pharmacy_name'   => 'required|string|max:255',
            'address'         => 'nullable|string',
            'phone'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'license_number'     => 'nullable|string|max:100',
            'pharmacist_name'    => 'nullable|string|max:255',
            'pharmacist_license' => 'nullable|string|max:100',
            'footer_note'     => 'nullable|string',
            'logo'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $setting = PharmacySetting::getSetting();

        // Proses upload logo jika ada file baru
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
                Storage::disk('public')->delete($setting->logo);
            }
            // Simpan logo baru
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $setting->update($validated);

        return redirect()->route('pengaturan')->with('success', 'Pengaturan apotek berhasil disimpan!');
    }
}
