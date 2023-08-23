<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use LaravelViews\Filters\BooleanFilter;

class UsersInactiveFilter extends BooleanFilter
{
    public $title =  "Activos / Inactivos";

    /**
     * Modify the current query when the filter is used
     *
     * @param Builder $query Current query
     * @param Array $value Associative array with the boolean value for each of the options
     * @return Builder Query modified
     */
    public function apply(Builder $query, $value): Builder
    {
        // $value['admin'] = true/false
        if ($value[0]) {
            $query->where('inactive', 0);
        }
        // $value['writer'] = true/false
        if ($value[1]) {
            $query->where('inactive', 1);
        }
        return $query;
    }

    /**
     * Defines the title and value for each option
     *
     * @return Array associative array with the title and values
     */
    public function options(): Array
    {
        return [
            'Activo' => 0,
            'Inactivo' => 1,
        ];
    }
}
