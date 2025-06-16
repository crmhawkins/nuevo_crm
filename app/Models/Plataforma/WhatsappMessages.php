<?php

namespace App\Models\Plataforma;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappMessages extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'chat_id',
        'message_id',
        'message_text',
        'message_to',
        'status'
    ];
}
