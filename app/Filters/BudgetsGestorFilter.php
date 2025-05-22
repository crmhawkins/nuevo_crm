<?php

namespace App\Filters;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Builder;
use LaravelViews\Filters\Filter;

class BudgetsGestorFilter extends Filter
{
    public $title =  "Gestores";

    /**
     * Modify the current query when the filter is used
     *
     * @param Builder $query Current query
     * @param $value Value selected by the user
     * @return Builder Query modified
     */
    public function apply(Builder $query, $value, $request): Builder
    {
        return $query->where('admin_user_id', $value);
    }

    /**
     * Defines the title and value for each option
     *
     * @return Array associative array with the title and values
     */
    public function options(): Array
    {

        $gestores = User::where('access_level_id', 4)->get();

        $data = [];

        foreach ($gestores as $key => $gestor) {
            $data += [$gestor->name => $gestor->id];

        }

        // dd($data);

        return $data;
    }
}
