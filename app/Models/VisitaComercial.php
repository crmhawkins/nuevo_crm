<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users\User;
use App\Models\Clients\Client;

class VisitaComercial extends Model
{
    use HasFactory;

    protected $fillable = [
        'comercial_id',
        'cliente_id',
        'nombre_cliente',
        'tipo_visita',
        'valoracion',
        'comentarios',
        'requiere_seguimiento',
        'fecha_seguimiento'
    ];

    protected $casts = [
        'fecha_seguimiento' => 'datetime',
        'requiere_seguimiento' => 'boolean'
    ];

    // Relaciones
    public function comercial()
    {
        return $this->belongsTo(User::class, 'comercial_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Client::class, 'cliente_id');
    }
}
