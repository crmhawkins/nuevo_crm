<?php

namespace App\Models\Budgets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetConceptSupplierUnits extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'budget_concept_supplier_units';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'budget_concept_id',
        'units',
        'selected'
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

}
