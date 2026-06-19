<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReturnDetail extends Model
{
    protected $fillable = [
        'stock_return_id',
        'medicine_id',
        'medicine_stock_id',
        'quantity',
        'purchase_price',
        'subtotal',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'subtotal'       => 'decimal:2',
    ];

    // Relasi ke stock return header
    public function stockReturn()
    {
        return $this->belongsTo(StockReturn::class);
    }

    // Relasi ke obat
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    // Relasi ke stok obat (batch)
    public function medicineStock()
    {
        return $this->belongsTo(MedicineStock::class, 'medicine_stock_id');
    }
}
