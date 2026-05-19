<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales';
    protected $primaryKey = 'id_suc';

    protected $fillable = [
        'nombre_suc',
        'dir_suc',
        'contacto_suc'
    ];

    public function administradores()
    {
        return $this->hasMany(Administrador::class, 'id_suc');
    }

    public function salas()
    {
        return $this->hasMany(Sala::class, 'id_suc2');
    }
}