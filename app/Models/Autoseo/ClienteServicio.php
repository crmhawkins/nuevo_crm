<?php

namespace App\Models\Autoseo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteServicio extends Model
{
    use HasFactory;

    protected $table = 'cliente_servicios';

    protected $fillable = [
        'autoseo_id',
        'nombre_servicio',
        'principal',
        'orden',
    ];

    protected $casts = [
        'principal' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Relación con Autoseo
     */
    public function autoseo()
    {
        return $this->belongsTo(Autoseo::class, 'autoseo_id');
    }

    /**
     * Scope para obtener solo servicios principales
     */
    public function scopePrincipales($query)
    {
        return $query->where('principal', true);
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden', 'asc');
    }
}

