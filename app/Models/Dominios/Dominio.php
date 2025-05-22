<?php

namespace App\Models\Dominios;

use App\Models\Clients\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dominio extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'dominios';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'dominio',
        'client_id',
        'date_end',
        'comentario',
        'date_start',
        'estado_id'
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
     * Obtener el cliente al que está vinculado
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Client::class,'client_id');
    }

    public function estadoName()
    {
        return $this->belongsTo(estadosDominios::class,'estado_id');
    }
}
