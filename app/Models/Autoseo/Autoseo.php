<?php

namespace App\Models\Autoseo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autoseo extends Model
{
    protected $table = 'autoseo';
    protected $fillable = [
        'client_name',
        'client_email',
        'json_home',
        'json_nosotros',
        'url',
        'last_seo',
        'next_seo',
        'created_at',
        'updated_at',
        'json_home_update',
        'json_nosotros_update',
        'username',
        'password',
        'reports',
        'user_app',
        'password_app',
        'json_storage',
        'json_competencia',
        'CompanyName',
        'AddressLine1',
        'Locality',
        'AdminDistrict',
        'PostalCode',
        'CountryRegion',
        'pin'
    ];
    use HasFactory;

    protected $casts = [
        'reports' => 'array',
        'json_storage' => 'array',
    ];

}
