<?php

namespace App\Models\Users;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserDepartament extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable ,SoftDeletes;


    protected $table = 'admin_user_department';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}
