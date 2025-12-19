<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCategories extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'services_categories';

    protected $fillable = [
        'name',
        'terms',
        'type',
        'inactive',
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
     * Scope para obtener todas las categorías de servicios que están en presupuestos,
     * incluyendo las que tienen deleted_at (soft deletes)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromBudgets($query)
    {
        $categoriasIds = \App\Models\Budgets\BudgetConcept::select('services_category_id')
            ->whereNotNull('services_category_id')
            ->distinct()
            ->pluck('services_category_id')
            ->toArray();

        return $query->withTrashed()
            ->whereIn('id', $categoriasIds);
    }

}
