<?php

namespace App\Models\Invoices;

use App\Models\Budgets\Budget;
use App\Models\Clients\Client;
use App\Models\PaymentMethods\PaymentMethod;
use App\Models\Projects\Project;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'invoices';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'budget_id',
        'reference',
        'reference_autoincrement_id',
        'admin_user_id',
        'client_id',
        'project_id',
        'payment_method_id',
        'invoice_status_id',
        'concept',
        'description',
        'gross',
        'base',
        'iva',
        'iva_percentage',
        'discount',
        'discount_percentage',
        'total',
        'paid_date',
        'paid_amount',
        'note',
        'observations',
        'billed_in_advance',
        'expiration_date',
        'seen_date',
        'cancelled_date',
        'partial',
        'show_summary',
        'partial_number',// si es la primera, segunda...
        'rectification',
        'is_ceuta',
        'created_at',
    ];


    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
         'created_at','updated_at', 'deleted_at',
    ];

    /**
     * Obtener el presupuesto al que está vinculada
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class,'budget_id');
    }

    /**
     * Obtener el usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminUser()
    {
        return $this->belongsTo(User::class,'admin_user_id');
    }

    /**
     * Obtener el cliente al que está vinculado
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Client::class,'client_id');
    }

    /**
     * Obtener la campaña al que está vinculado
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class,'project_id');
    }

    /**
     * Obtener el método de pago
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }


    public function invoiceConcepts()
    {
        return $this->hasMany(InvoiceConcepts::class,'invoice_id');

    }


    /**
     * Obtener el estado de la factura
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function invoiceStatus()
    {
        return $this->belongsTo(InvoiceStatus::class, 'invoice_status_id');
    }


    public static function averagePaidTime($facturas)
    {
        $totalDuration = 0;
        $count = 0;

        foreach ($facturas as $factura) {
            if ($factura->paid_date && $factura->created_at) {
                $paid_date = Carbon::parse($factura->paid_date);
                $duration = $paid_date->diffInDays($factura->created_at);
                $totalDuration += $duration;
                $count++;
            }
        }

        return $count > 0 ? $totalDuration / $count : 0; // Retorna la duración promedio
    }
}
