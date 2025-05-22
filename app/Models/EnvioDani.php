<?php

namespace App\Models;

use App\Models\Clients\Client;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnvioDani extends Model
{
    use HasFactory;

    protected $table = 'envio_dani';

    protected $fillable = [
        'kit_id',
        'cliente',
        'contacto',
        'telefono',
        'enviado',

    ];


}
