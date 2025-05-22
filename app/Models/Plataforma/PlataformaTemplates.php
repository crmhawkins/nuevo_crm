<?php

namespace App\Models\Plataforma;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlataformaTemplates extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'mensaje',
        'tipo_contenido',
        'contenido',
        'botones',
        'status',
        'rejection_reason',
        'template_id',
        'category',
        'language',
        'namespace',
    ];
}
