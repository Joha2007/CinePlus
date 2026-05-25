<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class Cliente extends Model implements AuthenticatableContract
{
    use HasApiTokens, HasFactory, Authenticatable;

    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';

    protected $fillable = [
        'nombre_cliente',
        'apellido_cliente',
        'correo_cli',
        'edad_cli',
        'contrasena_cli',
        'contacto_cli',
    ];

    protected $hidden = [
        'contrasena_cli',
    ];

    protected function casts(): array
    {
        return [
            'edad_cli' => 'integer',
        ];
    }

    // Para que Sanctum use la columna correcta como password
    public function getAuthPassword()
    {
        return $this->contrasena_cli;
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_cliente1');
    }
}