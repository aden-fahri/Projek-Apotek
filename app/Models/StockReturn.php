<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReturn extends Model
{
    protected $fillable = [
        'return_number',
        'supplier_id',
        'return_date',
        'reason',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'return_date'  => 'date',
        'total_amount' => 'decimal:2',
    ];

    // Relasi ke supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relasi ke detail return
    public function details()
    {
        return $this->hasMany(StockReturnDetail::class);
    }
}
