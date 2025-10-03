<?php

namespace App\Models;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjetivoComercial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'objetivo_comercials';

    protected $fillable = [
        'comercial_id',
        'admin_user_id',
        'fecha_inicio',
        'fecha_fin',
        'tipo_objetivo',
        'visitas_presenciales_diarias',
        'visitas_telefonicas_diarias',
        'visitas_mixtas_diarias',
        'planes_esenciales_mensuales',
        'planes_profesionales_mensuales',
        'planes_avanzados_mensuales',
        'ventas_euros_mensuales',
        'precio_plan_esencial',
        'precio_plan_profesional',
        'precio_plan_avanzado',
        'activo',
        'notas'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'ventas_euros_mensuales' => 'decimal:2',
        'precio_plan_esencial' => 'decimal:2',
        'precio_plan_profesional' => 'decimal:2',
        'precio_plan_avanzado' => 'decimal:2',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function comercial()
    {
        return $this->belongsTo(User::class, 'comercial_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeDelComercial($query, $comercialId)
    {
        return $query->where('comercial_id', $comercialId);
    }

    public function scopeVigentes($query)
    {
        $hoy = now()->toDateString();
        return $query->where('fecha_inicio', '<=', $hoy)
                    ->where('fecha_fin', '>=', $hoy);
    }

    // Métodos de cálculo
    public function getTotalVisitasDiariasAttribute()
    {
        return $this->visitas_presenciales_diarias + 
               $this->visitas_telefonicas_diarias + 
               $this->visitas_mixtas_diarias;
    }

    public function getTotalPlanesMensualesAttribute()
    {
        return $this->planes_esenciales_mensuales + 
               $this->planes_profesionales_mensuales + 
               $this->planes_avanzados_mensuales;
    }

    public function getValorTotalPlanesAttribute()
    {
        return ($this->planes_esenciales_mensuales * $this->precio_plan_esencial) +
               ($this->planes_profesionales_mensuales * $this->precio_plan_profesional) +
               ($this->planes_avanzados_mensuales * $this->precio_plan_avanzado);
    }
}
