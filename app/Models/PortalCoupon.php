<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalCoupon extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'coupons';

    protected $fillable = [
        'id',
        'discount',
        'used',
    ];
}
