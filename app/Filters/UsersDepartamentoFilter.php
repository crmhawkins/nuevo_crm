<?php

namespace App\Filters;

use App\Models\Users\UserDepartament;
use Illuminate\Database\Eloquent\Builder;
use LaravelViews\Filters\Filter;

class UsersDepartamentoFilter extends Filter
{
    public $title =  "Departamento";

    /**
     * Modify the current query when the filter is used
     *
     * @param Builder $query Current query
     * @param $value Value selected by the user
     * @return Builder Query modified
     */
    public function apply(Builder $query, $value, $request): Builder
    {
        return $query->where('admin_user_department_id', $value);
    }

    /**
     * Defines the title and value for each option
     *
     * @return Array associative array with the title and values
     */
    public function options(): Array
    {

        $departamentos = UserDepartament::all();

        $data = [];

        foreach ($departamentos as $key => $departamento) {
            $data += [$departamento->name => $departamento->id];

        }

        // dd($data);

        return $data;
    }
}
