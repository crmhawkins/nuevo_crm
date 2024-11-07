<?php

namespace App\Models\Budgets;

use App\Models\Invoices\Invoice;
use App\Models\Tasks\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'budgets';

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
        'is_ceuta',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'admin_user_id');
    }
    public function referencia()
    {
        return $this->belongsTo(\App\Models\Budgets\BudgetReferenceAutoincrement::class, 'reference_autoincrement_id');
    }
    public function estadoPresupuesto()
    {
        return $this->belongsTo(\App\Models\Budgets\BudgetStatu::class, 'budget_status_id');
    }
    public function cliente()
    {
        return $this->belongsTo(\App\Models\Clients\Client::class, 'client_id');
    }
    public function proyecto()
    {
        return $this->belongsTo(\App\Models\Projects\Project::class, 'project_id');
    }
    public function metodoPago()
    {
        return $this->belongsTo(\App\Models\PaymentMethods\PaymentMethod::class, 'payment_method_id');
    }
    public function budgetConcepts()
    {
        return $this->hasMany(BudgetConcept::class, 'budget_id');
    }
    public function factura()
    {
        return $this->hasOne(Invoice::class, 'budget_id');
    }
    public function cambiarEstadoPresupuesto($nuevoEstadoId)
    {
        if ($nuevoEstadoId == 4) {
            $this->tasks()->update(['task_status_id' => 4]);
        }
    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'budget_id');
    }
    public function getStatusColor()
    {
        $statusColors = [
            1 => '#FFA500', // Pendiente de confirmar: orange
            2 => '#FF6347', // Pendiente de aceptar: tomato red
            3 => '#008000', // Aceptado: green
            4 => '#808080', // Cancelado: grey
            5 => '#4682B4', // Finalizado: steel blue
            6 => '#0000FF', // Facturado: blue
            7 => '#8B4513', // Facturado parcialmente: saddle brown
        ];

        return $statusColors[$this->budget_status_id] ?? '#CCCCCC'; // Default to grey if not found
    }
}
