<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineStock extends Model
{
    use HasFactory;

    protected $table = 'medicine_stocks';

    protected $fillable = [
        'medicine_id',
        'purchase_order_id',
        'batch_number',
        'quantity',
        'initial_quantity',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'quantity'         => 'integer',
        'initial_quantity' => 'integer',
        'expiry_date'      => 'date',
    ];

    // Relasi ke obat
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    // Relasi ke purchase order (pembelian)
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
