<?php

namespace App\Models\Clients;

use App\Models\Invoices\Invoice;
use App\Models\Projects\Project;
use App\Models\Users\ClientUserOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'clients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'primerApellido',
        'segundoApellido',
        'admin_user_id',
        'contact_id',
        'client_id',
        'company',
        'email',
        'industry',
        'activity',
        'identifier',
        'cif',
        'birthdate',
        'country',
        'city',
        'province',
        'address',
        'zipcode',
        'fax',
        'phone',
        'web',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'pinterest',
        'is_client',
        'privacy_policy_accepted',
        'cookies_accepted',
        'newsletters_sending_accepted',
        'notes',
        'last_survey',
        'last_newsletter',
        'pin',
        'tipoCliente'
    ];

     /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function contacto() {
        return $this->hasMany(\App\Models\Contacts\Contact::class);
    }

    public function gestor() {
        return $this->belongsTo(\App\Models\Users\User::class,'admin_user_id');
    }

    public function cliente() {
        return $this->belongsTo(\App\Models\Clients\Client::class,'client_id');
    }

    public function emails() {
        return $this->hasMany(\App\Models\Clients\ClientEmail::class);
    }
    public function phones() {
        return $this->hasMany(\App\Models\Clients\ClientPhone::class);
    }
    public function webs() {
        return $this->hasMany(\App\Models\Clients\ClientWeb::class);
    }
    public function presupuestos() {
        return $this->hasMany(\App\Models\Budgets\Budget::class,'client_id');
    }
    public function presupuestosPorEstado($estadoId) {
        return $this->presupuestos()->where('budget_status_id', $estadoId)->get();
    }
    public function facturas(){
        return $this->hasMany(\App\Models\Invoices\Invoice::class, 'client_id');

    }
    public function facturasPorEstado($estadoId) {
        return $this->facturas()->where('invoice_status_id', $estadoId)->withCount('invoiceConcepts')->get();

    }

    public function averagePaidTime($estado)
    {
        $facturas = $this->facturasPorEstado($estado);
        return Invoice::averagePaidTime($facturas);
    }
    public function dominios(){
        return $this->hasMany(\App\Models\Dominios\Dominio::class, 'client_id');
    }
    public function campañas() {
        return $this->hasMany(Project::class,'client_id');
    }

    public function userOrders()
    {
        return $this->hasMany(ClientUserOrder::class);
    }

    public function totalPresupuestosPorMes()
    {
        $year = date('Y'); // Obtener el año actual
        $presupuestos = $this->presupuestos()
                             ->selectRaw('sum(total) as total, MONTH(created_at) as month')
                             ->whereYear('created_at', $year)
                             ->groupBy('month')
                             ->get()
                             ->keyBy('month');

        // Preparar un arreglo con todos los meses inicializados a 0
        $months = array_fill(1, 12, ['total' => 0]);

        // Reemplazar los datos en el arreglo por los datos reales
        foreach ($presupuestos as $month => $data) {
            $months[$month] = $data;
        }

        return $months;
    }

    public function totalFacturasPorMes()
    {
        $year = date('Y'); // Obtener el año actual
        $facturas = $this->facturas()
                         ->selectRaw('sum(total) as total, MONTH(paid_date) as month')
                         ->whereYear('created_at', $year)
                         ->groupBy('month')
                         ->get()
                         ->keyBy('month');

        // Preparar un arreglo con todos los meses inicializados a 0
        $months = array_fill(1, 12, ['total' => 0]);

        // Reemplazar los datos en el arreglo por los datos reales
        foreach ($facturas as $month => $data) {
            $months[$month] = $data;
        }

        return $months;
    }

}
