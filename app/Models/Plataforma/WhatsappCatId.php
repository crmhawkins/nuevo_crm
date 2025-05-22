<?php

namespace App\Models\Plataforma;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappCatId extends Model
{
    use HasFactory;
    protected $table = 'whatsapp_cat_id';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'category',
    ];
}
