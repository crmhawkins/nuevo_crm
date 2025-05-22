<?php

namespace App\Models\Budgets;

use App\Models\Clients\Client;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetSend extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'budgets_sends';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'admin_user_id',
        'budget_id',
        'budget_reference',
        'client_id',
        'file_name',
        'acceptance_conds',
        'date_send',
        'IP',
        'file_delete',
        'emails',
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
        return $this->belongsTo(User::class,'admin_user_id');
    }
        
    /**
     * Obtener el cliente al que está vinculado
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class,'client_id');
    }

    /**
     * Obtener el presupuesto
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function budgetId()
    {
        return $this->belongsTo(Budget::class,'budget_id');
    }
}
