<?php

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'clients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'admin_user_id',
        'contact_id',
        'client_id',
        'company',
        'email',
        'industry',
        'activity',
        'identifier',
        'cif',
        'birthdate',
        'country',
        'city',
        'province',
        'address',
        'zipcode',
        'fax',
        'phone',
        'web',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'pinterest',
        'is_client',
        'privacy_policy_accepted',
        'cookies_accepted',
        'newsletters_sending_accepted',
        'notes',
        'last_survey',
        'last_newsletter',
    ];

     /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function contacto() {
        return $this->hasMany(\App\Models\Contacts\Contact::class);
    }

    public function gestor() {
        return $this->belongsTo(\App\Models\Users\User::class,'admin_user_id');
    }

    public function cliente() {
        return $this->belongsTo(\App\Models\Clients\Client::class,'client_id');
    }

    public function emails() {
        return $this->hasMany(\App\Models\Clients\ClientEmail::class);
    }
    public function phones() {
        return $this->hasMany(\App\Models\Clients\ClientPhone::class);
    }
    public function webs() {
        return $this->hasMany(\App\Models\Clients\ClientWeb::class);
    }
    public function presupuestos() {
        return $this->hasMany(\App\Models\Budgets\Budget::class);
    }
    public function facturas(){
        return $this->hasMany(\App\Models\Invoices\Invoice::class, 'client_id');
    }
    public function dominios(){
        return $this->hasMany(\App\Models\Dominios\Dominio::class, 'client_id');
    }
}
