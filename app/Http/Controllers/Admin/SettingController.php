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
            'phone'           => 'nullable|string|regex:/^08[0-9]{8,11}$/',
            'email'           => 'nullable|email|max:255',
            'license_number'  => 'nullable|string|max:100',
            'tax_rate'        => 'nullable|numeric|min:0|max:100',
            'logo'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'phone.regex' => 'Nomor telepon harus diawali dengan 08 dan hanya berisi angka.',
        ]);

        $setting = PharmacySetting::getSetting();

        // Proses upload logo jika ada file baru (Simpan permanen di public/images agar bisa dipush ke github)
        if ($request->hasFile('logo')) {
            $fileName = 'logo.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move(public_path('images'), $fileName);
            $validated['logo'] = 'images/' . $fileName;
        }

        $setting->update($validated);

        return redirect()->route('pengaturan')->with('success', 'Pengaturan apotek berhasil disimpan!');
    }
}
