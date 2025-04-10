<?php

namespace App\Models;

use App\Models\Clients\Client; // Importa el modelo Client
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'purchase_type',
        'payment_status',
        'amount',
        'stripe_charge_id',
        'status',
        'customer_name',
        'customer_email',
        'customer_address',
        'template'
    ];

    // Relación con el modelo Client
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
