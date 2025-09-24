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
        'ultima_sincronizacion'
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at', 'ultima_sincronizacion'
    ];

    /**
     * Casts para atributos
     */
    protected $casts = [
        'ultima_sincronizacion' => 'datetime',
        'sincronizado' => 'boolean',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
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
}
