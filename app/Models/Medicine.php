<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category_id',
        'medicine_group_id',
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
        return $this->belongsTo(MedicineGroup::class);
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
}
