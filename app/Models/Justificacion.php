<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users\User;

class Justificacion extends Model
{
    use HasFactory;

    protected $table = 'justificacions';

    protected $fillable = [
        'admin_user_id',
        'nombre_justificacion',
        'tipo_justificacion',
        'archivos',
        'metadata'
    ];

    protected $casts = [
        'archivos' => 'array',
        'metadata' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}

