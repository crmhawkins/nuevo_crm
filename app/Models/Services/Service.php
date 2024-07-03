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
}
