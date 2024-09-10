<?php

namespace App\Models\Services;

use GuzzleHttp\Promise\Each;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'services';

    protected $fillable = [
        'services_categories_id',
        'title',
        'concept',
        'price',
        'estado',
        'inactive',
        'order'
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    // Definir la relación con BudgetConcept
    public function budgetConcepts()
    {
        return $this->hasMany(\App\Models\Budgets\BudgetConcept::class, 'service_id');
    }


    public function servicoNombre() {
        return $this->belongsTo(\App\Models\Services\ServiceCategories::class,'services_categories_id');
    }

    public function calcularSumaPresupuestos() {
        $presupuestos = $this->budgetConcepts()
                             ->with('presupuesto')
                             ->get()
                             ->sum(function($concepto) {
                                 return $concepto->presupuesto->monto_presupuesto;
                             });

        return $presupuestos;
    }
    public function calcularPrecioMedio($precioPorHora)
    {
        $totalPrecio = 0;
        $totalTareas = 0;
        // Obtener todos los conceptos de presupuesto relacionados con este servicio
        $budgetConcepts = $this->budgetConcepts()->with('task')->get();
        // Recorrer cada concepto de presupuesto y sus tareas
        foreach ($budgetConcepts as $budgetConcept) {
            foreach ($budgetConcept->task as $task) {
                if ($task->real_time) {
                    // Convertir el tiempo a segundos
                    $tiempo = explode(':', $task->real_time);
                    $segundos = ($tiempo[0] * 3600) + ($tiempo[1] * 60) + $tiempo[2];
                    // Convertir los segundos a horas
                    $horas = $segundos / 3600;
                    // Calcular el precio basado en las horas reales y el precio por hora
                    $precio = $horas * $precioPorHora;
                    $totalPrecio += $precio;
                    $totalTareas++;
                }
            }
        }
        // Calcular el precio medio
        if ($totalTareas > 0) {
            return $totalPrecio / $totalTareas;
        }
        return 0; // Si no hay tareas, el precio medio es 0
    }
}
