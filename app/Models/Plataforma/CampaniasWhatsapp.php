<?php

namespace App\Models\Plataforma;

use Illuminate\Database\Eloquent\Model;

class CampaniasWhatsapp extends Model
{
    protected $table = 'campanias';

    protected $fillable = [
        'nombre',
        'mensaje',
        'fecha_lanzamiento',
        'clientes',
        'estado',
        'categoria_cliente'
    ];

    protected $casts = [
        'clientes' => 'array',
        'fecha_lanzamiento' => 'datetime',
        'estado' => 'int',
    ];
    
}
