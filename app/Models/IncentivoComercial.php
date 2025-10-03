<?php

namespace App\Models;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncentivoComercial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'incentivo_comercials';

    protected $fillable = [
        'comercial_id',
        'admin_user_id',
        'fecha_inicio',
        'fecha_fin',
        'porcentaje_venta',
        'porcentaje_adicional',
        'min_clientes_mensuales',
        'min_ventas_mensuales',
        'precio_plan_esencial',
        'precio_plan_profesional',
        'precio_plan_avanzado',
        'activo',
        'notas'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'porcentaje_venta' => 'decimal:2',
        'porcentaje_adicional' => 'decimal:2',
        'min_ventas_mensuales' => 'decimal:2',
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
    public function calcularIncentivo($ventasTotales, $clientesTotales)
    {
        $incentivoBase = $ventasTotales * ($this->porcentaje_venta / 100);
        
        $incentivoAdicional = 0;
        if ($clientesTotales >= $this->min_clientes_mensuales) {
            $incentivoAdicional = $ventasTotales * ($this->porcentaje_adicional / 100);
        }
        
        return [
            'incentivo_base' => $incentivoBase,
            'incentivo_adicional' => $incentivoAdicional,
            'total_incentivo' => $incentivoBase + $incentivoAdicional,
            'cumple_adicional' => $clientesTotales >= $this->min_clientes_mensuales
        ];
    }
}
