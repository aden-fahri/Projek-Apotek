<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category_id',
        'group_id',
        'unit_id',
        'purchase_price',
        'selling_price',
        'min_stock',
        'description',
        'requires_prescription',
        'is_active',
    ];

    protected $casts = [
        'purchase_price'        => 'decimal:2',
        'selling_price'         => 'decimal:2',
        'requires_prescription' => 'boolean',
        'is_active'             => 'boolean',
    ];

    // Relasi ke kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke golongan
    public function medicineGroup()
    {
        return $this->belongsTo(MedicineGroup::class, 'group_id');
    }

    // Relasi ke satuan
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Relasi ke detail transaksi
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // Relasi ke stok obat
    public function stocks()
    {
        return $this->hasMany(MedicineStock::class);
    }
}
