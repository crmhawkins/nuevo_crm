<?php

namespace App\Models\Incidence;

use App\Models\Budgets\Budget;
use App\Models\Clients\Client;
use App\Models\Suppliers\Supplier;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incidences extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'incidences';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'budget_id',
        'supplier_id',
        'gestor_id',
        'admin_user_id',
        'client_id',
        'status_id'
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
     * Obtener el usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
    public function gestor_id()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
    public function presupuesto()
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }
    public function cliente()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function proveedor()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function estado()
    {
        return $this->belongsTo(IncidenceStatus::class, 'status_id');
    }

}
