<?php

namespace App\Filters;

use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetStatu;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Builder;
use LaravelViews\Filters\Filter;

class BudgetsEstadosFilter extends Filter
{
    public $title =  "Estados";

    /**
     * Modify the current query when the filter is used
     *
     * @param Builder $query Current query
     * @param $value Value selected by the user
     * @return Builder Query modified
     */
    public function apply(Builder $query, $value, $request): Builder
    {
        return $query->where('budget_status_id', $value);
    }

    /**
     * Defines the title and value for each option
     *
     * @return Array associative array with the title and values
     */
    public function options(): Array
    {

        $estados = BudgetStatu::all();
        // dd($estados);

        $data = [];

        foreach ($estados as $key => $estado) {
            $data += [$estado->name => $estado->id];
        }
        // dd($data);

        return $data;
    }
}
