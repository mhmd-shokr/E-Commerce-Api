<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    public $timestamps = false; 
    protected $fillable=[
        'order_id',
        'type',
        'name',
        'email',
        'phone',
        'locality',
        'address',
        'city',
        'state',
        'country',
        'landmark',
        'postal_code',
    ];
}
