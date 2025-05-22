<?php

namespace App\Models\Company;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CompanyDetails extends Model
{

    protected $table = 'company_details';

    /**
     * Atributos asignados en masa. Por seguridad.
     *
     * @var array
     */
    protected $fillable = [
        'logo',
        'company_name',
        'nif',
        'address',
        'bank_account_data',
        'price_hour',
        'postCode',
        'town',
        'province',
        'paypal',
        'telephone',
        'email',
        'website',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'pinterest',
        'certificado',
        'contrasena'
    ];

    /**
     * Atributos que deben mutarse a fechas.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at',
    ];



}

