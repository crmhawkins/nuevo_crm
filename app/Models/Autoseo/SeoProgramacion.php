<?php

namespace App\Models\Autoseo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoProgramacion extends Model
{
    use HasFactory;

    protected $table = 'seo_programaciones';

    protected $fillable = [
        'autoseo_id',
        'fecha_programada',
        'estado',
    ];

    protected $casts = [
        'fecha_programada' => 'date',
    ];

    /**
     * Relación con Autoseo
     */
    public function autoseo()
    {
        return $this->belongsTo(Autoseo::class, 'autoseo_id');
    }

    /**
     * Scope para obtener programaciones de una fecha específica
     */
    public function scopeFechaProgramada($query, $fecha)
    {
        return $query->where('fecha_programada', $fecha);
    }

    /**
     * Scope para obtener solo pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
}

