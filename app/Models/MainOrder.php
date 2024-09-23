<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainOrder extends Model
{
    use HasFactory;
    public function orders()
    {
        return $this->hasMany(Order::class, 'main_id', 'id');
    }
}
