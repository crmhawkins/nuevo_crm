<?php

namespace App\Models\Plataforma;

use Illuminate\Database\Eloquent\Model;

class WhatsappContacts extends Model
{
    protected $table = 'whatsapp_contacts';

    protected $fillable = [
        'client_id',
        'campania_id',
        'status',
        'sent',
    ];

    public function cliente()
{
    return $this->belongsTo(\App\Models\Clients\Client::class, 'client_id');
}
}