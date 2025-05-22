<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_user_id',
        'client_id',
        'priority_id',
        'name',
        'description',
        'notes',
        'deadline',
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
    public function cliente() {
        return $this->belongsTo(\App\Models\Clients\Client::class,'client_id');
    }
    public function prioridad() {
        return $this->belongsTo(\App\Models\Prioritys\Priority::class,'priority_id');
    }
    public function presupuestos(){
        return $this->hasMany(\App\Models\Budgets\Budget::class, 'project_id');
    }
    public function presupuestosPorEstado($estadoId) {
        return $this->presupuestos()->where('budget_status_id', $estadoId)->get();
    }
}
