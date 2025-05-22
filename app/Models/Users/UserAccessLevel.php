<?php

namespace App\Models\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserAccessLevel extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'admin_user_access_level';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];
    const FULL_ADMINISTRATOR = 1;
    const ACCESS_LEVEL_GERENTE = 2;
    const ACCESS_LEVEL_CONTABLE = 3;
    const ACCESS_LEVEL_GESTOR = 4;
    const ACCESS_LEVEL_PERSONAL = 5;
}
