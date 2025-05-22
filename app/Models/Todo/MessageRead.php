<?php

namespace App\Models\Todo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageRead extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'message_reads';

    protected $fillable = [
        'message_id',
        'admin_user_id',
        'is_read',
        'read_at',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}
