<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'transaction_date',
        'total',
        'tax',
        'grand_total',
        'paid_amount',
        'change_amount',
        'payment_method',
        'customer_name',
        'notes',
        'status',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'total'            => 'decimal:2',
        'tax'              => 'decimal:2',
        'grand_total'      => 'decimal:2',
        'paid_amount'      => 'decimal:2',
        'change_amount'    => 'decimal:2',
    ];

    // Relasi ke kasir (user)
    public function kasir()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke detail transaksi
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
