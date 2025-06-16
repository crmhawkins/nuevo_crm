<?php

namespace App\Models\Plataforma;

use Illuminate\Database\Eloquent\Model;

class WhatsappContacts extends Model
{
    protected $table = 'whatsapp_contacts';

    protected $fillable = [
        'client_id',
        'campania_id',
        'last_message',
        'chat_id'
    ];

    public function cliente()
{
    return $this->belongsTo(\App\Models\Clients\Client::class, 'client_id');
}
}