<?php

namespace App\Models\Budgets;

use App\Models\Alerts\Alert;
use App\Models\Invoices\Invoice;
use App\Models\Logs\LogActions;
use App\Models\Tasks\Task;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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
        return $this->belongsTo(User::class, 'admin_user_id');
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
        switch($nuevoEstadoId) {
            case 4:
                $this->tasks()->update(['task_status_id' => 4]);
                break;
            case 5:
                $usuarios = User::where('access_level_id',3)->where('inactive',0)->get();

                foreach ($usuarios as $usuario) {
                    $alert = Alert::create([
                        'reference_id' => $this->id,
                        'admin_user_id' => $usuario->id,
                        'stage_id' => 5,
                        'status_id' => 1,
                        'activation_datetime' => Carbon::now(),
                        'description' => 'Presupuesto ' . $this->reference.' esta finalizado y no esta facturado.'
                    ]);
                }

                break;
            default:
                break;
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

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($budget) {
            $changed = $budget->getDirty(); // Obtiene los campos que han cambiado
            if (isset(Auth::user()->id)) {
            $userId =Auth::user()->id; // Obtiene el ID del usuario autenticado
            } else {
                $userId = 1;
            }
            foreach ($changed as $field => $newValue) {

                $oldValue = $budget->getOriginal($field);

                LogActions::create([
                    'tipo' => 2,
                    'admin_user_id' => $userId,
                    'action' => 'Actualizar presupuesto' . $budget->reference,
                    'description' => 'De  "'.(is_null($oldValue) ? 'N/A' : (string)$oldValue).'"  a  "'. (is_null($newValue) ? 'N/A' : (string)$newValue).'"',
                    'reference_id' => $budget->id,
                ]);
            }
        });
    }
}
