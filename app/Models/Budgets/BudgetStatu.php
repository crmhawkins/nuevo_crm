<?php

namespace App\Models\Budgets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetStatu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'budget_status';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',

    ];

     /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    // Para los ID
     /**
     * TIPO CONCEPTO: Proveedor
     *
     * @var string
     */
    const PENDING_CONFIRMATION = 1;

    /**
     * TIPO CONCEPTO: Propio
     *
     * @var string
     */
    const PENDING_ACCEPT = 2;
}
