<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineGroup extends Model
{
    protected $table = 'medicine_groups';

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    // Relasi ke obat
    public function medicines()
    {
        return $this->hasMany(Medicine::class, 'group_id');
    }
}
