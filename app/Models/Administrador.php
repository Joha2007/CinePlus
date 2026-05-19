<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    protected $table = 'administradores';
    protected $primaryKey = 'id_admin';

    protected $fillable = [
        'id_suc',
        'nombre_adm',
        'apellido_adm',
        'correo_adm',
        'contacto_adm',
        'contrasena_adm'
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_suc');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_admin1');
    }
}