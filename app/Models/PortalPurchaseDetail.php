<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalPurchaseDetail extends Model
{
    use HasFactory;

    protected $table = 'purchase_details';

    protected $fillable = [
        'purchase_id',
        'marca',
        'historia',
        'servicios',
        'redes',
        'dominio',
        'email',
        'address',
        'phone',
        'politica',
        'hosting',
        'dominio_externo',
        'imagenes',
        'color_principal',
        'color_secundario'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}