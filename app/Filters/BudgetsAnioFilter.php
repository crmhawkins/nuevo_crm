<?php

namespace App\Filters;

use App\Models\Budgets\Budget;
use App\Models\Budgets\BudgetStatu;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use LaravelViews\Filters\Filter;

class BudgetsAnioFilter extends Filter
{
    public $title =  "Anio";

    /**
     * Modify the current query when the filter is used
     *
     * @param Builder $query Current query
     * @param $value Value selected by the user
     * @return Builder Query modified
     */
    public function apply(Builder $query, $value, $request): Builder
    {
        if (!$value) {
            // Si no se ha seleccionado un valor, aplicar el año actual por defecto
            $value = now()->year;
        }
        return $query->whereYear('creation_date', $value);
    }

    /**
     * Defines the title and value for each option
     *
     * @return Array associative array with the title and values
     */
    public function options(): Array
    {

        $anios = Budget::select(DB::raw('YEAR(created_at) as year'))
            ->groupBy('year')
            ->orderBy('year', 'desc') // Suponiendo que quieras los años en orden descendente
            ->get()
            ->pluck('year');
        // dd($anios);
        $data = [];
        foreach ($anios as $key => $anio) {
            $anioString = (string) $anio;
            // dd($anioString);
            $data['- '.$anioString] = $anio;

        }
        // dd( $data );
        return $data;
    }
}
