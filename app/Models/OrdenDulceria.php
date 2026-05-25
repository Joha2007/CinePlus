<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenDulceria extends Model
{
    protected $table = 'orden_dulcerias';
    protected $primaryKey = 'id_orden';

    protected $fillable = [
        'id_reserva2',
        'total',
        'cant_produc'
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'cant_produc' => 'integer',
        ];
    }

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva2');
    }

    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'orden_productos',
            'id_orden1',
            'id_producto1'
        )->withPivot('cantidad');
    }
}