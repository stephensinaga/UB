<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    public function material()
    {
        return $this->belongsTo( Material::class, 'id_material');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class,   'id_unit');
    }

}
