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
        'created_at',
        'updated_at',
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
    // Subconsulta A: "Actualizar estado en kit digital"
    $subA = DB::table('log_actions')
        ->select('reference_id', DB::raw('MAX(created_at) as ultima_fecha'))
        ->where('action', 'Actualizar estado en kit digital')
        ->where(function ($q) {
            $q->where('enviado', 0)
              ->orWhereNull('enviado');
        })
        ->groupBy('reference_id');

    // Subconsulta B: "Actualizar sasak en kit digital"
    $subB = DB::table('log_actions')
        ->select('reference_id', DB::raw('MAX(created_at) as ultima_fecha'))
        ->where('action', 'Actualizar sasak en kit digital')
        ->where(function ($q) {
            $q->where('enviado', 0)
              ->orWhereNull('enviado');
        })
        ->groupBy('reference_id');

    // Unión de ambas
    $union = $subA->unionAll($subB);

    // Subconsulta ya sin agrupación extra
    $subquery = DB::table(DB::raw("({$union->toSql()}) as ultimos_logs"))
        ->mergeBindings($subA); // 💡 importante

    return $query
        ->joinSub($subquery, 'ultimos_logs', function ($join) {
            $join->on('log_actions.reference_id', '=', 'ultimos_logs.reference_id')
                 ->on('log_actions.created_at', '=', 'ultimos_logs.ultima_fecha');
        })
        ->join('ayudas', 'ayudas.id', '=', 'log_actions.reference_id')
        ->where(function ($q) use ($fechaLimite, $fecha) {
            $q->where(function ($q1) use ($fechaLimite) {
                $q1->where('log_actions.action', 'Actualizar estado en kit digital')
                   ->where('log_actions.created_at', '<=', $fechaLimite);
            })
            ->orWhere(function ($q2) use ($fecha) {
                $q2->where('log_actions.action', 'Actualizar sasak en kit digital')
                   ->where(function ($subq) use ($fecha) {
                       $subq->where('ayudas.sasak', '<=', $fecha);
                            // ->orWhereNull('ayudas.sasak');
                   });
            });
        })
        ->select(
            'log_actions.reference_id',
            'log_actions.action',
            'log_actions.created_at as ultima_fecha',
            'ayudas.contratos',
            'ayudas.estado',
            'ayudas.sasak'
        );
}




    public static function mas6Meses()
    {
        $seisMesesAtras = now()->subMonths(6);

        // Subconsulta: obtener la última fecha de JUSTIFICADO o SEGUNDA JUSTIFICACIÓN (REALIZADA) por reference_id
        $subquery = DB::table('log_actions as l1')
            ->select('l1.id')
            ->where('l1.action', 'Actualizar estado en kit digital')
            ->where(function ($q) {
                $q->where('l1.description', 'like', '%a  "JUSTIFICADO"%')
                ->orWhere('l1.description', 'like', '%a  "SEGUNDA JUSTIFICACIÓN (REALIZADA)"%');
            })
            ->whereRaw('l1.created_at = (
                SELECT MAX(l2.created_at)
                FROM log_actions l2
                WHERE l2.reference_id = l1.reference_id
                AND l2.action = l1.action
                AND (
                    l2.description LIKE "%a  \\"JUSTIFICADO\\"%" OR
                    l2.description LIKE "%a  \\"SEGUNDA JUSTIFICACIÓN (REALIZADA)\\"%"
                )
            )');

        return self::whereIn('log_actions.id', $subquery)
            ->join('ayudas', 'ayudas.id', '=', 'log_actions.reference_id')
            ->whereNotIn('ayudas.estado', [1, 6, 11, 27, 22, 19, 18, 16, 20])
            ->where('log_actions.created_at', '<=', now()->subMonths(6))
            ->select(
                'log_actions.reference_id',
                'log_actions.description',
                'log_actions.created_at as ultima_fecha',
                'ayudas.estado',
                'ayudas.contratos'
            );
    }


    public static function automatizacionEmailsLogs_old($query, $fechaLimite, $fecha)
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
