<?php

namespace App\Models\Plataforma;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeloMensajes extends Model
{
    use HasFactory;
    protected $table = 'modelos_mensajes';
    protected $fillable = ['mensaje', 'campania_id'];
}
