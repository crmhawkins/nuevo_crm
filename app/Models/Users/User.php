<?php

namespace App\Models\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Accounting\AssociatedExpenses;
use App\Models\Bajas\Baja;
use App\Models\Clients\Client;
use App\Models\Contratos\Contrato;
use App\Models\Holidays\Holidays;
use App\Models\Holidays\HolidaysPetitions;
use App\Models\Llamadas\Llamada;
use App\Models\Nominas\Nomina;
use App\Models\Projects\Project;
use App\Models\Todo\Todo;
use App\Models\Todo\TodoUsers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    protected $table = 'admin_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'access_level_id',
        'admin_user_department_id',
        'admin_user_position_id',
        'name',
        'surname',
        'username',
        'password',
        'role',
        'image',
        'email',
        'seniority_years',
        'seniority_months',
        'holidays_days',
        'inactive',
        'is_dark'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function llamadas() {
        return $this->hasMany(Llamada::class, 'admin_user_id');
    }
    public function posicion() {
        return $this->belongsTo(\App\Models\Users\UserPosition::class,'admin_user_position_id');
    }
    public function departamento() {
        return $this->belongsTo(\App\Models\Users\UserDepartament::class,'admin_user_department_id');
    }
    public function acceso() {
        return $this->belongsTo(\App\Models\Users\UserAccessLevel::class,'access_level_id');
    }
    public function tareas(){
        return $this->hasMany(\App\Models\Tasks\Task::class,  'admin_user_id');
    }
    public function nominas(){
        return $this->hasMany(Nomina::class, 'admin_user_id');
    }
    public function contratos(){
        return $this->hasMany(Contrato::class, 'admin_user_id');
    }
    public function vacaciones(){
        return $this->hasMany(HolidaysPetitions::class, 'admin_user_id');
    }
    public function bajas(){
        return $this->hasMany(Baja::class, 'admin_user_id');
    }
    public function vacacionesDias(){
        return $this->hasMany(Holidays::class, 'admin_user_id');
    }
    public function tareasGestor(){
        return $this->hasMany(\App\Models\Tasks\Task::class, 'gestor_id');
    }
    public function campañas(){
        return $this->hasMany(Project::class, 'admin_user_id');
    }
    public function clientes(){
        return $this->hasMany(Client::class, 'admin_user_id');
    }
    public function peticiones(){
        return $this->hasMany(\App\Models\Petitions\Petition::class, 'admin_user_id');
    }
    public function todos() {
        return $this->hasManyThrough(Todo::class,TodoUsers::class,'admin_user_id', 'id', 'id','todo_id');
    }
    public function presupuestos(){
        return $this->hasMany(\App\Models\Budgets\Budget::class, 'admin_user_id');
    }
    public function presupuestosPorEstado($estadoId) {
        return $this->presupuestos()->where('budget_status_id', $estadoId)->get();
    }
    public function peticionesPendientes() {
        return $this->peticiones()->where('finished', false)->get();
    }
    public function eventos() {
        return $this->hasMany(\App\Models\Events\Event::class, 'admin_user_id');
    }
    public function jornadas() {
        return $this->hasMany(\App\Models\Jornada\Jornada::class, 'admin_user_id');
    }
    public function activeJornada() {
        return $this->jornadas()->where('is_active', true)->first();
    }
    public function activeLlamada() {
        return $this->llamadas()->where('is_active', true)->first();
    }

    public function totalWorkedTimeToday() {
        $todayJornadas = $this->jornadas()->whereDate('start_time', Carbon::today())->get();
        $totalWorkedSeconds = 0;

        foreach ($todayJornadas as $jornada) {
            $totalWorkedSeconds += $jornada->total_worked_time;
        }

        return $totalWorkedSeconds;
    }

    public function ordenes()
    {
        return AssociatedExpenses::join('purchase_order', 'associated_expenses.purchase_order_id', '=', 'purchase_order.id')
        ->join('budget_concepts', 'purchase_order.budget_concept_id', '=', 'budget_concepts.id')
        ->join('budgets', 'budget_concepts.budget_id', '=', 'budgets.id')
        ->where('budgets.admin_user_id', $this->id)
        ->where('associated_expenses.state', 'PENDIENTE')
        ->whereNull('associated_expenses.aceptado_gestor')
        ->select('purchase_order.*' , 'budgets.admin_user_id as gestor')  // Asegúrate de seleccionar los campos de las órdenes de compra
        ->get();  // Asegúrate de seleccionar los campos de las órdenes de compra
    }

}
