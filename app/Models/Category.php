<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
    ];

    // Relasi ke obat
    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }
}
