<?php

namespace App\Models\Suite;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suite extends Model
{
    use HasFactory;

    protected $table = 'suite';

    protected $fillable = [
        'user',
        'password',
        'logged_at',
        'created_at',
        'updated_at',
    ];
}
