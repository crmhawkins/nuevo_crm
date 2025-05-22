<?php

namespace App\Models\Plataforma;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappStats extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_stats';

    protected $fillable = [
        'messages_sent',
        'messages_received',
        'messages_failed',
        'messages_read',
        'response_received',
        'accepted_campania',
        'rejected_campania',
    ];
}
