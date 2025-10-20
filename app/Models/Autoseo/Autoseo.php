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
        'company_context',
        'pin'
    ];
    use HasFactory;

    protected $casts = [
        'reports' => 'array',
        'json_storage' => 'array',
    ];

    public function reports()
    {
        return $this->hasMany(AutoseoReport::class);
    }

    /**
     * Relación con SeoProgramacion
     */
    public function programaciones()
    {
        return $this->hasMany(SeoProgramacion::class, 'autoseo_id');
    }

    /**
     * Relación con ClienteServicio
     */
    public function servicios()
    {
        return $this->hasMany(ClienteServicio::class, 'autoseo_id');
    }
}
