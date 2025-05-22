<?php

namespace App\Models\Budgets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetReferenceAutoincrement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'budget_reference_autoincrements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference_autoincrement',
        'year',
        'month_num',
        'month',
        'month_full',
        'day',
        'letter_months',

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
