<?php

namespace App\Models\Budgets;

use App\Models\PurcharseOrde\PurcharseOrder;
use App\Models\Tasks\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetConcept extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'budget_concepts';

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
        'is_facturado',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function presupuesto()
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    public function concepto()
    {
        return $this->belongsTo(BudgetConceptType::class, 'concept_type_id');
    }

    public function servicio()
    {
        return $this->belongsTo(\App\Models\Services\Service::class, 'service_id');
    }

    public function servicioCategoria()
    {
        return $this->belongsTo(\App\Models\Services\ServiceCategories::class, 'services_category_id');
    }

    public function unidades()
    {
        return $this->hasMany(BudgetConceptSupplierUnits::class, 'budget_concept_id');
    }
    public function proveedor()
    {
        return $this->hasMany(BudgetConceptSupplierRequest::class, 'budget_concept_id');
    }

    public function orden()
    {
        return $this->hasOne(PurcharseOrder::class, 'budget_concept_id');
    }

    public function task()
    {
        return $this->hasMany(Task::class, 'budget_concept_id');
    }
}
