<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'medicine_id',
        'quantity',
        'purchase_price',
        'subtotal',
        'batch_number',
        'expired_date',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'subtotal'       => 'decimal:2',
        'expired_date'   => 'date',
    ];

    // Relasi ke purchase order
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // Relasi ke obat
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
