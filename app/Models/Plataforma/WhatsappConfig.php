<?php
namespace App\Models\Plataforma;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappConfig extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_config';

    protected $fillable = [
        'company_name',
        'company_phone',
        'company_cat_id',
        'company_address',
        'company_mail',
        'company_web',
        'company_description',
        'company_logo',
        'company_apikey',
        'fullfilled'
    ];

    protected $casts = [
        'clientes' => 'array', // Si decides almacenar como JSON
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}