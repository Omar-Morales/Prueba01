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
        'email',
        'phone',
        'address',
        'photo',
        'status',
    ];

    public function ventas(){
        return $this->hasMany(Venta::class);
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo && file_exists(storage_path('app/public/' . $this->photo))) {
            return asset('storage/' . $this->photo);
        }
        return asset('assets/images/customers.png');
    }

}
