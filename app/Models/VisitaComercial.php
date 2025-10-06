<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Users\User;
use App\Models\Clients\Client;

class VisitaComercial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'comercial_id',
        'cliente_id',
        'nombre_cliente',
        'tipo_visita',
        'valoracion',
        'comentarios',
        'requiere_seguimiento',
        'fecha_seguimiento',
        'plan_interesado',
        'precio_plan',
        'estado',
        'observaciones_plan',
        'audio_file',
        'audio_duration',
        'audio_recorded_at'
    ];

    protected $casts = [
        'fecha_seguimiento' => 'datetime',
        'requiere_seguimiento' => 'boolean',
        'precio_plan' => 'decimal:2',
        'audio_recorded_at' => 'datetime'
    ];

    // Constantes para estados
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_ACEPTADO = 'aceptado';
    const ESTADO_RECHAZADO = 'rechazado';
    const ESTADO_EN_PROCESO = 'en_proceso';

    // Constantes para planes
    const PLAN_ESENCIAL = 'esencial';
    const PLAN_PROFESIONAL = 'profesional';
    const PLAN_AVANZADO = 'avanzado';

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
