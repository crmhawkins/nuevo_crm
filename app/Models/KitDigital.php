<?php

namespace App\Models;

use App\Models\Clients\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KitDigital extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ayudas';


    protected $fillable = [
        'estado',
        'servicio_id',
        'comercial_id',
        'cliente_id',
        'mensaje_interpretado',
        'mensaje',
        'empresa',
        'segmento',
        'cliente',
        'expediente',
        'contratos',
        'gestor',
        'fecha_actualizacion',
        'importe',
        'estado_factura',
        'banco',
        'fecha_acuerdo',
        'plazo_maximo_entrega',
        'contacto',
        'empleados',
        'interesado',
        'pendiente',
        'telefono',
        'nombre',
        'nif',
        'comunidad',
        'cp',
        'cnae',
        'anno_inicio',
        'email',
        'pago_aprovi',
        'comentario',
        'nuevo_comentario',
        'nota',
        'date',
        'enviado'
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function Client() {
        return $this->belongsTo(Client::class,'cliente_id');
    }
    public function estados() {
        return $this->belongsTo(KitDigitalEstados::class,'estado');
    }
    public function servicios() {
        return $this->belongsTo(KitDigitalServicios::class,'servicio_id');
    }

}
