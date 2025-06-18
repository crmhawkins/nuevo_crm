<?php

namespace App\Models\Plataforma;

use Illuminate\Database\Eloquent\Model;

class WhatsappContacts extends Model
{
    protected $table = 'whatsapp_contacts';

    protected $fillable = [
        'name',
        'phone',
        'wid'
    ];

    public function cliente()
{
    return $this->belongsTo(\App\Models\Clients\Client::class, 'client_id');
}
}