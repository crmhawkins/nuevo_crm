<?php

namespace App\Models\Budgets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'budgets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference',
        'reference_autoincrement_id',
        'admin_user_id',
        'client_id',
        'project_id',
        'payment_method_id',
        'budget_status_id',
        'concept',
        'creation_date',
        'description',
        'gross',
        'base',
        'iva',
        'iva_percentage',
        'total',
        'discount',
        'temp',
        'expiration_date',
        'accepted_date',
        'cancelled_date',
        'note',
        'billed_in_advance',
        'retention_percentage',
        'total_retention',
        'invoiced_advance',
        'commercial_id',
        'level_commission',
        'duracion',
        'cuotas_mensuales',
        'order_column',
    ];

     /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function usuario() {
        return $this->belongsTo(\App\Models\Users\User::class,'admin_user_id');
    }

    public function referencia() {
        return $this->belongsTo(\App\Models\Budgets\BudgetReferenceAutoincrement::class,'reference_autoincrement_id');
    }

    public function estadoPresupuesto() {
        return $this->belongsTo(\App\Models\Budgets\BudgetStatu::class,'budget_status_id');
    }

    public function cliente() {
        return $this->belongsTo(\App\Models\Clients\Client::class,'client_id');
    }

    public function proyecto() {
        return $this->belongsTo(\App\Models\Projects\Project::class,'project_id');
    }

    public function metodoPago() {
        return $this->belongsTo(\App\Models\PaymentMethods\PaymentMethod::class,'payment_method_id');
    }

    public function budgetConcepts()
    {
        return $this->hasMany(BudgetConcept::class, 'budget_id');
    }


}
