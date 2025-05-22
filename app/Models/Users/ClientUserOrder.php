<?php

namespace App\Models\Users;

use App\Models\Clients\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientUserOrder extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'client_id', 'order'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
