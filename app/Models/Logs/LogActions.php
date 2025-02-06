<?php

namespace App\Models\Logs;

use App\Models\Budgets\Budget;
use App\Models\Invoices\Invoice;
use App\Models\KitDigital;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogActions extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'log_actions';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'tipo',
        'admin_user_id',
        'action',
        'description',
        'reference_id',

    ];


    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];


    public function usuario()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'admin_user_id');
    }

    public function tipo()
    {
        return $this->belongsTo(\App\Models\Logs\LogsTipes::class, 'tipo');
    }

    public function ayudas(){
        return $this->belongsTo(KitDigital::class, 'reference_id');
    }

    public function presupuesto(){
        return $this->belongsTo(Budget::class, 'reference_id');
    }

    public function factura(){
        return $this->belongsTo(Invoice::class, 'reference_id');
    }


    public function cliente(){
        switch($this->tipo){
            case 1:
                return $this->ayudas->cliente;
            case 2:
                return $this->presupuesto->cliente;
            case 3:
                return $this->factura->cliente;
            default:
                return null;

        }
    }

}
