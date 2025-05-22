<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    protected $table = 'admin_user';

    protected $fillable = [
        'username',
        'password',
    ];

    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }
}
