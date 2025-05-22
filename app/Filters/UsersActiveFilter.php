<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use LaravelViews\Filters\Filter;

class UsersActiveFilter extends Filter
{
    public $title =  "Nivel de Acceso";

    /**
     * Modify the current query when the filter is used
     *
     * @param Builder $query Current query
     * @param $value Value selected by the user
     * @return Builder Query modified
     */
    public function apply(Builder $query, $value, $request)
    {
        return $query->where('access_level_id', $value);
    }

    /**
     * Defines the title and value for each option
     *
     * @return Array associative array with the title and values
     */
    public function options()
    {

        return [
            'Full Administrator' => 1,
            'Gerente' => 2,
            'Contable' => 3,
            'Gestor' => 4,
            'Personal' => 5,
            'Comercial' => 6,
        ];
    }
}
