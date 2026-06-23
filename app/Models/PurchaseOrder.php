<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'invoice_number',
        'supplier_id',
        'user_id',
        'order_date',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'order_date'   => 'date',
        'total_amount' => 'decimal:2',
    ];

    // Relasi ke supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relasi ke detail pembelian
    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    // Relasi ke user (admin yang membuat PO)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
