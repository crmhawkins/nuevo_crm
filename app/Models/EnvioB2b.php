<?php

namespace App\Models;

use App\Models\Clients\Client;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnvioB2b extends Model
{
    use HasFactory;

    protected $table = 'envio_b2b';

    protected $fillable = [
        'nombre',
        'telefono',
        'enviado',
        'mensaje_interpretado',
        'mensaje',
    ];


}
