<?php

namespace App\Models\Autoseo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autoseo extends Model
{
    protected $table = 'autoseo';
    protected $fillable = ['client', 'client_email', 'json_home', 'json_nosotros', 'url', 'last_seo', 'next_seo', 'created_at', 'updated_at', 'json_home_update', 'json_nosotros_update'];
    use HasFactory;
}