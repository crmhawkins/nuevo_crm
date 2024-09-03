<?php

namespace App\Models\Statistics;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Statistics extends Model
{
    use SoftDeletes;

    protected $table = 'balance_trimester';
    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'trimester',
        'month',
        'year',
        'quantity',
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
