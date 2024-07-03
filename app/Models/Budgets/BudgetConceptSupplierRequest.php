<?php

namespace App\Models\Budgets;

use App\Models\Suppliers\Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetConceptSupplierRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'budget_concept_supplier_requests';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'budget_concept_id',
        'supplier_id',
        'mail',
        'option_number',
        'price',
        'accepted',
        'sent_date',
        'accepted_date',
        'selected',
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];


    /**
     * Obtener el concepto de presupuesto al que pertenece
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function budgetConcept()
    {
        return $this->belongsTo(BudgetConcept::class,'budget_concept_id');
    }

    /**
     * Obtener el proveedor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    /**
     * Obtener el proveedor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unidades()
    {
        return $this->hasMany(BudgetConceptSupplierUnits::class,'budget_concept_id');
    }
}
