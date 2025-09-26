<?php

namespace App\Models;

use App\Models\Clients\Client;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'color',
        'estado',
        'tipo',
        'ubicacion',
        'notas_internas',
        'recordatorios',
        'cliente_id',
        'gestor_id',
        'creado_por',
        'actualizado_por',
        'es_recurrente',
        'patron_recurrencia',
        'fecha_fin_recurrencia',
        'configuracion_recurrencia',
        'notificar_cliente',
        'notificar_gestor',
        'minutos_recordatorio',
        'resultados',
        'acciones_siguientes',
        'requiere_seguimiento',
        'fecha_seguimiento'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'fecha_fin_recurrencia' => 'date',
        'fecha_seguimiento' => 'date',
        'configuracion_recurrencia' => 'array',
        'es_recurrente' => 'boolean',
        'notificar_cliente' => 'boolean',
        'notificar_gestor' => 'boolean',
        'requiere_seguimiento' => 'boolean'
    ];

    // Constantes para estados
    const ESTADO_PROGRAMADA = 'programada';
    const ESTADO_CONFIRMADA = 'confirmada';
    const ESTADO_EN_PROGRESO = 'en_progreso';
    const ESTADO_COMPLETADA = 'completada';
    const ESTADO_CANCELADA = 'cancelada';

    // Constantes para tipos
    const TIPO_REUNION = 'reunion';
    const TIPO_LLAMADA = 'llamada';
    const TIPO_VISITA = 'visita';
    const TIPO_PRESENTACION = 'presentacion';
    const TIPO_SEGUIMIENTO = 'seguimiento';
    const TIPO_OTRO = 'otro';

    // Constantes para patrones de recurrencia
    const RECURRENCIA_DIARIA = 'daily';
    const RECURRENCIA_SEMANAL = 'weekly';
    const RECURRENCIA_MENSUAL = 'monthly';
    const RECURRENCIA_ANUAL = 'yearly';

    /**
     * Relación con el cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Client::class, 'cliente_id');
    }

    /**
     * Relación con el gestor asignado
     */
    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    /**
     * Relación con el usuario que creó la cita
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Relación con el usuario que actualizó la cita
     */
    public function actualizador()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    /**
     * Scope para citas de un cliente específico
     */
    public function scopeDelCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    /**
     * Scope para citas de un gestor específico
     */
    public function scopeDelGestor($query, $gestorId)
    {
        return $query->where('gestor_id', $gestorId);
    }

    /**
     * Scope para citas en un rango de fechas
     */
    public function scopeEnRango($query, $fechaInicio, $fechaFin)
    {
        return $query->where(function ($q) use ($fechaInicio, $fechaFin) {
            $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
              ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
              ->orWhere(function ($subQ) use ($fechaInicio, $fechaFin) {
                  $subQ->where('fecha_inicio', '<=', $fechaInicio)
                       ->where('fecha_fin', '>=', $fechaFin);
              });
        });
    }

    /**
     * Scope para citas por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para citas por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para citas recurrentes
     */
    public function scopeRecurrentes($query)
    {
        return $query->where('es_recurrente', true);
    }

    /**
     * Scope para citas que requieren seguimiento
     */
    public function scopeRequierenSeguimiento($query)
    {
        return $query->where('requiere_seguimiento', true);
    }

    /**
     * Scope para citas próximas (próximas 7 días)
     */
    public function scopeProximas($query, $dias = 7)
    {
        $fechaInicio = now();
        $fechaFin = now()->addDays($dias);
        
        return $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                    ->where('estado', '!=', self::ESTADO_CANCELADA);
    }

    /**
     * Scope para citas vencidas
     */
    public function scopeVencidas($query)
    {
        return $query->where('fecha_fin', '<', now())
                    ->where('estado', '!=', self::ESTADO_COMPLETADA)
                    ->where('estado', '!=', self::ESTADO_CANCELADA);
    }

    /**
     * Obtener la duración de la cita en minutos
     */
    public function getDuracionAttribute()
    {
        return $this->fecha_inicio->diffInMinutes($this->fecha_fin);
    }

    /**
     * Verificar si la cita está en progreso
     */
    public function getEstaEnProgresoAttribute()
    {
        $ahora = now();
        return $ahora->between($this->fecha_inicio, $this->fecha_fin);
    }

    /**
     * Verificar si la cita es hoy
     */
    public function getEsHoyAttribute()
    {
        return $this->fecha_inicio->isToday();
    }

    /**
     * Verificar si la cita es mañana
     */
    public function getEsMananaAttribute()
    {
        return $this->fecha_inicio->isTomorrow();
    }

    /**
     * Obtener el estado formateado
     */
    public function getEstadoFormateadoAttribute()
    {
        $estados = [
            self::ESTADO_PROGRAMADA => 'Programada',
            self::ESTADO_CONFIRMADA => 'Confirmada',
            self::ESTADO_EN_PROGRESO => 'En Progreso',
            self::ESTADO_COMPLETADA => 'Completada',
            self::ESTADO_CANCELADA => 'Cancelada'
        ];

        return $estados[$this->estado] ?? $this->estado;
    }

    /**
     * Obtener el tipo formateado
     */
    public function getTipoFormateadoAttribute()
    {
        $tipos = [
            self::TIPO_REUNION => 'Reunión',
            self::TIPO_LLAMADA => 'Llamada',
            self::TIPO_VISITA => 'Visita',
            self::TIPO_PRESENTACION => 'Presentación',
            self::TIPO_SEGUIMIENTO => 'Seguimiento',
            self::TIPO_OTRO => 'Otro'
        ];

        return $tipos[$this->tipo] ?? $this->tipo;
    }

    /**
     * Obtener el color del estado
     */
    public function getColorEstadoAttribute()
    {
        $colores = [
            self::ESTADO_PROGRAMADA => '#6b7280', // Gris
            self::ESTADO_CONFIRMADA => '#3b82f6', // Azul
            self::ESTADO_EN_PROGRESO => '#f59e0b', // Amarillo
            self::ESTADO_COMPLETADA => '#10b981', // Verde
            self::ESTADO_CANCELADA => '#ef4444'  // Rojo
        ];

        return $colores[$this->estado] ?? '#6b7280';
    }

    /**
     * Obtener el icono del tipo
     */
    public function getIconoTipoAttribute()
    {
        $iconos = [
            self::TIPO_REUNION => 'fa-users',
            self::TIPO_LLAMADA => 'fa-phone',
            self::TIPO_VISITA => 'fa-map-marker-alt',
            self::TIPO_PRESENTACION => 'fa-presentation',
            self::TIPO_SEGUIMIENTO => 'fa-tasks',
            self::TIPO_OTRO => 'fa-calendar'
        ];

        return $iconos[$this->tipo] ?? 'fa-calendar';
    }

    /**
     * Verificar si la cita está próxima (dentro de los minutos de recordatorio)
     */
    public function getEstaProximaAttribute()
    {
        $tiempoRecordatorio = $this->fecha_inicio->subMinutes($this->minutos_recordatorio);
        return now()->gte($tiempoRecordatorio) && now()->lt($this->fecha_inicio);
    }

    /**
     * Obtener el nombre del cliente o "Sin cliente"
     */
    public function getNombreClienteAttribute()
    {
        return $this->cliente ? $this->cliente->name : 'Sin cliente';
    }

    /**
     * Obtener el nombre del gestor o "Sin asignar"
     */
    public function getNombreGestorAttribute()
    {
        return $this->gestor ? $this->gestor->name : 'Sin asignar';
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Evento al crear una cita
        static::created(function ($cita) {
            // Aquí se pueden agregar notificaciones automáticas
        });

        // Evento al actualizar una cita
        static::updated(function ($cita) {
            // Aquí se pueden agregar notificaciones de cambios
        });
    }
}