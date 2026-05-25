<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class Administrador extends Model implements AuthenticatableContract
{
    use HasApiTokens, HasFactory, Authenticatable;

    protected $table = 'administradores';
    protected $primaryKey = 'id_admin';

    protected $fillable = [
        'id_suc',
        'nombre_adm',
        'apellido_adm',
        'correo_adm',
        'contacto_adm',
        'contrasena_adm',
    ];

    protected $hidden = [
        'contrasena_adm',
    ];

    // Para que Sanctum use la columna correcta como password
    public function getAuthPassword()
    {
        return $this->contrasena_adm;
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_suc');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_admin1');
    }
}