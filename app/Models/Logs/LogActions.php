<?php

namespace App\Models\Logs;

use App\Models\Budgets\Budget;
use App\Models\Invoices\Invoice;
use App\Models\KitDigital;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
                return $this->presupuesto->cliente->company ?? $this->presupuesto->cliente->name;
            case 3:
                return $this->factura->cliente->company ?? $this->factura->cliente->name;
            default:
                return null;

        }
    }

    public static function automatizacionEmailsLogs($query, $fechaLimite, $fecha)
    {
        // Subconsulta: obtener última acción relevante por reference_id
        $subquery = DB::table('log_actions')
            ->select('reference_id', DB::raw('MAX(created_at) as ultima_fecha'))
            ->where('action', 'Actualizar estado en kit digital')
            ->where(function ($query) {
                $query->where('enviado', 0)
                      ->orWhereNull('enviado');
            })
            ->groupBy('reference_id');
    
        // Juntamos con log_actions para recuperar datos completos
        return $query
            ->joinSub($subquery, 'ultimos_logs', function ($join) {
                $join->on('log_actions.reference_id', '=', 'ultimos_logs.reference_id')
                     ->on('log_actions.created_at', '=', 'ultimos_logs.ultima_fecha');
            })
            ->join('ayudas', 'ayudas.id', '=', 'log_actions.reference_id')
            ->where('log_actions.created_at', '<=', $fechaLimite)
            ->where('log_actions.action', 'Actualizar estado en kit digital')
            ->where(function ($q) use ($fecha) {
                $q->where('ayudas.sasak', '<=', $fecha)
                  ->orWhereNull('ayudas.sasak');
            })
            ->select(
                'log_actions.reference_id',
                'log_actions.created_at as ultima_fecha',
                'ayudas.contratos',
                'ayudas.estado',
                'ayudas.sasak',
            );
    }
        
    public static function registroCorreosEnviados($data)
    {
        DB::table('log_actions')
        ->where('reference_id', $data->reference_id) // Si el reference_id coincide con el de la tabla log_actions
        ->update(['enviado' => 0]); // Cambiamos enviado a True

        // Agregamos el registro del correo enviado
        DB::table('correos_enviados')->insert([
            'ref_id' => $data->reference_id,
            'contrato' => $data->contratos,
            'respondido' => 0, // Respondido lo ponemos en False, se acaba de enviar el correo
            'date' => now() // Fecha de hoy
        ]);
    } 
}
