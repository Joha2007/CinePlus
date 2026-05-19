<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';

    protected $fillable = [
        'nombre_cliente',
        'apellido_cliente',
        'correo_cli',
        'edad_cli',
        'contrasena_cli',
        'contacto_cli'
    ];

    protected function casts(): array
    {
        return [
            'edad_cli' => 'integer',
        ];
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_cliente1');
    }
}