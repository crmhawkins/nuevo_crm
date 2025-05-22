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
        // cupons boolean true ya usaste el cupon - false o null 

        // Crea una modificacion en la tabla de usuarios temporal y añadere una columna donde se registre el cupon y luego cuando vamos a pagar aplico ese cupon lo establezco aqui como que ya esta usado en el pago y en vez de enviar los datos a stgripe simulo 
    ];

    // Relación con el modelo Client
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
