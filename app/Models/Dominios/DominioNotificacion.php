<?php

namespace App\Models\Dominios;

use App\Models\Clients\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DominioNotificacion extends Model
{
    use HasFactory;

    protected $table = 'dominio_notificaciones';

    protected $fillable = [
        'dominio_id',
        'client_id',
        'tipo_notificacion',
        'fecha_envio',
        'estado',
        'token_enlace',
        'metodo_pago_solicitado',
        'fecha_caducidad',
        'error_mensaje'
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'fecha_caducidad' => 'date',
    ];

    /**
     * Relación con el dominio
     */
    public function dominio()
    {
        return $this->belongsTo(Dominio::class, 'dominio_id');
    }

    /**
     * Relación con el cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Marcar como enviado
     */
    public function marcarEnviado()
    {
        $this->update([
            'estado' => 'enviado',
            'fecha_envio' => now()
        ]);
    }

    /**
     * Marcar como fallido
     */
    public function marcarFallido($error = null)
    {
        $this->update([
            'estado' => 'fallido',
            'fecha_envio' => now(),
            'error_mensaje' => $error
        ]);
    }
}
