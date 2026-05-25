<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'id_admin1',
        'nom_productos',
        'descripcion',
        'stock',
        'precio_producto',
        'img',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'precio_producto' => 'decimal:2',
        ];
    }

    public function administrador()
    {
        return $this->belongsTo(Administrador::class, 'id_admin1');
    }

    public function ordenes()
    {
        return $this->belongsToMany(
            OrdenDulceria::class,
            'orden_productos',
            'id_producto1',
            'id_orden1'
        );
    }
}