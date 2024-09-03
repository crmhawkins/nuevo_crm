<?php

namespace App\Models\Contacts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'contacts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_user_id',
        'civil_status_id',
        'client_id',
        'name',
        'dni',
        'email',
        'birthdate',
        'sex',
        'country',
        'city',
        'province',
        'address',
        'zipcode',
        'academic_info',
        'academic_title',
        'work_activity',
        'fax',
        'phone',
        'web',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'pinterest',
        'privacy_policy_accepted',
        'cookies_accepted',
        'newsletters_sending_accepted',
        'notes',
    ];

     /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function statusCivil() {
        return $this->belongsTo(\App\Models\Contacts\CivilStatus::class,'civil_status_id');
    }

    public function user() {
        return $this->belongsTo(\App\Models\Users\User::class,'admin_user_id');
    }
    public function client() {
        return $this->belongsTo(\App\Models\Clients\Client::class,'client_id');
    }

}
