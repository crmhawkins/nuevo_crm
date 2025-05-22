<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';  // Asegúrate de que el nombre de la tabla es correcto

    // Aquí puedes definir los campos que pueden ser llenados de forma masiva
    protected $fillable = [
        'id', 'client_id', 'purchase_type', 'amount', 'status', 'stripe_charge_id', 'template'
    ];


}
