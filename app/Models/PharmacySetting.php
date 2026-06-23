<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacySetting extends Model
{
    protected $fillable = [
        'pharmacy_name',
        'address',
        'phone',
        'email',
        'logo',
        'license_number',
        'tax_rate',
    ];

    /**
     * Ambil pengaturan apotek (singleton — selalu ambil baris pertama).
     * Jika belum ada, buat dengan nilai default.
     */
    public static function getSetting(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'pharmacy_name'    => 'Apotek MediFlow',
                'address'          => 'Jl. Kesehatan No. 1, Kota',
                'phone'            => '021-0000000',
                'email'            => 'apotek@mediflow.id',
                'license_number'      => 'SIA-000/000/00',
                'pharmacist_name'     => 'Apt. Nama Apoteker, S.Farm.',
                'pharmacist_license'  => 'SIPA-000/000/00',
                'footer_note'      => 'Terima kasih telah mempercayai pelayanan kami.',
            ]
        );
    }
}
