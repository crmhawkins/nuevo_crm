<?php

namespace App\Models\Budgets;

use App\Models\Suppliers\Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetConceptSupplierRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'budget_concept_supplier_requests';

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

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function budgetConcept()
    {
        return $this->belongsTo(BudgetConcept::class, 'budget_concept_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function unidades()
    {
        return $this->hasMany(BudgetConceptSupplierUnits::class, 'budget_concept_id');
    }
}
