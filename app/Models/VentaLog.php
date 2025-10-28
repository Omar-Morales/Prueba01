<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaLog  extends Model
{
    use HasFactory;

    protected $table = 'venta_logs';

    protected $fillable = [
        'venta_id',
        'accion',
        'user_id',
        'ip',
        'datos_antes',
        'datos_despues',
    ];

    public $timestamps = true;
}
