<?php

namespace App\Models\Budgets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetConcept extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'budget_concepts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'budget_id',
        'concept_type_id',
        'service_id',
        'services_category_id',
        'title',
        'concept',
        'units',
        'purchase_price',
        'benefit_margin',
        'sale_price',
        'discount',
        'total',
        'total_no_discount',
    ];

     /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function presupuesto() {
        return $this->belongsTo(\App\Models\Budgets\Budget::class,'budget_id');
    }
    public function concepto() {
        return $this->belongsTo(\App\Models\Budgets\BudgetConceptType::class,'concept_type_id');
    }
    public function servicio() {
        return $this->belongsTo(\App\Models\Services\Service::class,'service_id');
    }
    public function servicioCategoria() {
        return $this->belongsTo(\App\Models\Services\ServiceCategories::class,'services_category_id');
    }

    /**
     * Obtener la Unidades de Concepto Proveedor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unidades()
    {
        return $this->hasMany(BudgetConceptSupplierUnits::class,'budget_concept_id');
    }
}
