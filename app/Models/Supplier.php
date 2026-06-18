<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'city',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi ke purchase orders
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
