<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruc',
        'name',
        'phone',
        'address',
        'status',
    ];

    public function ventas(){
        return $this->hasMany(Venta::class);
    }
}
