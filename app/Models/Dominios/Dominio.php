<?php

namespace App\Models\Dominios;

use App\Models\Clients\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dominio extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'dominios';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'dominio',
        'client_id',
        'date_end',
        'comentario',
        'date_start',
        'estado_id',
        'precio_compra',
        'precio_venta',
        'iban',
        'sincronizado',
        'ultima_sincronizacion',
        'fecha_activacion_ionos',
        'fecha_renovacion_ionos',
        'sincronizado_ionos',
        'ultima_sincronizacion_ionos'
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at', 'ultima_sincronizacion', 'fecha_activacion_ionos', 'fecha_renovacion_ionos', 'ultima_sincronizacion_ionos'
    ];

    /**
     * Casts para atributos
     */
    protected $casts = [
        'ultima_sincronizacion' => 'datetime',
        'sincronizado' => 'boolean',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'fecha_activacion_ionos' => 'datetime',
        'fecha_renovacion_ionos' => 'datetime',
        'sincronizado_ionos' => 'boolean',
        'ultima_sincronizacion_ionos' => 'datetime',
    ];


    /**
     * Obtener el cliente al que está vinculado
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Client::class,'client_id');
    }

    public function estadoName()
    {
        return $this->belongsTo(estadosDominios::class,'estado_id');
    }

    /**
     * Obtener el margen de beneficio
     */
    public function getMargenBeneficioAttribute()
    {
        if ($this->precio_compra && $this->precio_venta) {
            return $this->precio_venta - $this->precio_compra;
        }
        return 0;
    }

    /**
     * Obtener el porcentaje de margen
     */
    public function getPorcentajeMargenAttribute()
    {
        if ($this->precio_compra && $this->precio_venta && $this->precio_compra > 0) {
            return (($this->precio_venta - $this->precio_compra) / $this->precio_compra) * 100;
        }
        return 0;
    }

    /**
     * Verificar si el dominio está sincronizado
     */
    public function isSincronizado()
    {
        return $this->sincronizado && $this->ultima_sincronizacion;
    }

    /**
     * Marcar como sincronizado
     */
    public function marcarSincronizado()
    {
        $this->update([
            'sincronizado' => true,
            'ultima_sincronizacion' => now()
        ]);
    }

    /**
     * Obtener la fecha de última sincronización formateada
     */
    public function getUltimaSincronizacionFormateadaAttribute()
    {
        if (!$this->ultima_sincronizacion) {
            return 'N/A';
        }
        
        try {
            return $this->ultima_sincronizacion->format('d/m/Y H:i');
        } catch (\Exception $e) {
            return 'Fecha inválida';
        }
    }

    /**
     * Obtener la fecha de activación en IONOS formateada
     */
    public function getFechaActivacionIonosFormateadaAttribute()
    {
        if (!$this->fecha_activacion_ionos) {
            return 'N/A';
        }
        
        try {
            return $this->fecha_activacion_ionos->format('d/m/Y H:i');
        } catch (\Exception $e) {
            return 'Fecha inválida';
        }
    }

    /**
     * Obtener la fecha de renovación en IONOS formateada
     */
    public function getFechaRenovacionIonosFormateadaAttribute()
    {
        if (!$this->fecha_renovacion_ionos) {
            return 'N/A';
        }
        
        try {
            return $this->fecha_renovacion_ionos->format('d/m/Y H:i');
        } catch (\Exception $e) {
            return 'Fecha inválida';
        }
    }

    /**
     * Verificar si el dominio está sincronizado con IONOS
     */
    public function isSincronizadoIonos()
    {
        return $this->sincronizado_ionos && $this->ultima_sincronizacion_ionos;
    }

    /**
     * Marcar como sincronizado con IONOS
     */
    public function marcarSincronizadoIonos()
    {
        $this->update([
            'sincronizado_ionos' => true,
            'ultima_sincronizacion_ionos' => now()
        ]);
    }
}
