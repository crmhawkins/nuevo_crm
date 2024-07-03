<?php

namespace App\Models\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    protected $table = 'admin_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'access_level_id',
        'admin_user_department_id',
        'admin_user_position_id',
        'name',
        'surname',
        'username',
        'password',
        'role',
        'image',
        'email',
        'seniority_years',
        'seniority_months',
        'holidays_days',
        'inactive',
        'is_dark'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function posicion() {
        return $this->belongsTo(\App\Models\Users\UserPosition::class,'admin_user_position_id');
    }
    public function departamento() {
        return $this->belongsTo(\App\Models\Users\UserDepartament::class,'admin_user_department_id');
    }
    public function acceso() {
        return $this->belongsTo(\App\Models\Users\UserAccessLevel::class,'access_level_id');
    }


}
