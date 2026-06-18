<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'medicine_id',
        'quantity',
        'price',
        'purchase_price',
        'subtotal',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'subtotal'       => 'decimal:2',
    ];

    // Relasi ke transaksi header
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relasi ke obat
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
