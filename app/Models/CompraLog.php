<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompraLog  extends Model
{
    use HasFactory;

    protected $table = 'compra_logs';

    protected $fillable = [
        'compra_id',
        'accion',
        'user_id',
        'ip',
        'datos_antes',
        'datos_despues',
    ];

    public $timestamps = true;
}
