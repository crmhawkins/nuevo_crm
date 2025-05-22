<?php

namespace App\Models\PurcharseOrde;

use App\Models\Clients\Client;
use App\Models\PaymentMethods\PaymentMethod;
use App\Models\Suppliers\Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurcharseOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'purchase_order';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supplier_id',
        'budget_concept_id',
        'client_id',
        'project_id',
        'payment_method_id',
        'bank_id',
        'units',
        'amount',
        'shipping_date',
        'note',
        'sent',
        'cancelled',
        'status',
    ];
    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function concepto() {
        return $this->belongsTo(\App\Models\Budgets\BudgetConcept::class,'budget_concept_id');
    }
    public function cliente() {
        return $this->belongsTo(Client::class,'client_id');
    }
    public function payMethod() {
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }
    public function Proveedor() {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }
    public function associatedExpense() {
        return $this->hasOne(\App\Models\Accounting\AssociatedExpenses::class, 'purchase_order_id');
    }

}
