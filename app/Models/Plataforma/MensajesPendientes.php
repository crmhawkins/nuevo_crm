<?php

namespace App\Models\Plataforma;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MensajesPendientes extends Model
{
    use HasFactory;
    protected $table = 'mensajes_pendientes';
    protected $fillable = ['tlf', 'message', 'status', 'client_id'];
}
