<?php

namespace App\Models\Plataforma;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappAlerts extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'link', 'status'];
}
