<?php

namespace App\Models\Budgets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetConceptType extends Model
{
    use HasFactory;

    protected $table = 'budget_concept_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];
}
