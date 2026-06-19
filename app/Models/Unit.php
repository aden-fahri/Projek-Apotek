<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'units';

    protected $fillable = [
        'name',
        'abbreviation',
    ];

    // Relasi ke obat
    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }
}
