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

    /**
     * ACEPTADO
     *
     * @var string
     */
    const ACCEPTED = 3;

    /**
     * CANCELADO
     *
     * @var string
     */
    const CANCELLED = 4;

    /**
     * FACTURADO
     *
     * @var string
     */
    const FACTURADO = 6;

    /**
     * FACTURADO PARCIALMENTE
     *
     * @var string
     */
    const FACTURADO_PARCIALMENTE = 7;

    /**
     * FINALIZADO
     *
     * @var string
     */
    const FINALIZADO = 5;

    /**
     * FINALIZADO PROPIO
     *
     * @var string
     */
    const FINALIZADO_PROPIO = 8;

    /**
     * ESPERANDO PAGO PARCIAL
     *
     * @var string
     */
    const ESPERANDO_PAGO_PARCIAL = 9;
}
