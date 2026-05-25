<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenProducto extends Model
{
    protected $table = 'orden_productos';

    public $timestamps = false;

    protected $fillable = [
        'id_orden1',
        'id_producto1',
        'cantidad',
    ];
}