<?php

namespace App\Filters;

use App\Models\Users\UserPosition;
use Illuminate\Database\Eloquent\Builder;
use LaravelViews\Filters\Filter;

class UsersCargoFilter extends Filter
{
    public $title =  "Cargo";

    /**
     * Modify the current query when the filter is used
     *
     * @param Builder $query Current query
     * @param $value Value selected by the user
     * @return Builder Query modified
     */
    public function apply(Builder $query, $value, $request): Builder
    {
        return $query->where('admin_user_position_id', $value);
    }

    /**
     * Defines the title and value for each option
     *
     * @return Array associative array with the title and values
     */
    public function options(): Array
    {
        $posiciones = UserPosition::all();

        $data = [];

        foreach ($posiciones as $key => $posicion) {
            $data += [$posicion->name => $posicion->id];

        }

        // dd($data);

        return $data;
    }
}
