<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'user_id','subtotal','discount','tax','total',
        'name','phone','locality','address','city',
        'state','country','landmark','zip','type',
        'status','is_shipping_different','delivered_date','canceled_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function orderItems()
    {
        return $this->hasMany(OrderItems::class);
    }
}
