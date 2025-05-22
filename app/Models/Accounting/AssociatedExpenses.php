<?php

namespace App\Models\Accounting;

use App\Models\Budgets\Budget;
use App\Models\Other\BankAccounts;
use App\Models\PaymentMethods\PaymentMethod;
use App\Models\PurcharseOrde\PurcharseOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssociatedExpenses extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'associated_expenses';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'reference',
        'quantity',
        'bank_id',
        'date',
        'received_date',
        'payment_method_id',
        'budget_id',
        'purchase_order_id',
        'state',
        'aceptado_gestor',
        'documents',
        'iva',
        'date_aceptado',
        'categoria_id',
        'total'
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    /**
     * Obtener los bancos
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bankAccount()
    {
        return $this->belongsTo(BankAccounts::class, 'bank_id');
    }

    public function categoria(){
        return $this->belongsTo(CategoriaGastosAsociados::class,'categoria_id');
    }

    /**
     * Obtener Ordenes de compra
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function OrdenCompra()
    {
        return $this->belongsTo(PurcharseOrder::class, 'purchase_order_id');
    }

    /**
     * Obtener Presupuesto
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

}
