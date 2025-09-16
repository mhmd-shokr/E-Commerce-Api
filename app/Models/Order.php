<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'subtotal',
        'discount',
        'tax',
        'total',
        'payment_method',
        'number',
        'status',
        'payment_status',
        'delivered_date',
        'canceled_date',
        'coupon_id',
        'coupon_code',
    ];
    
    public static function booted(){
        static::creating(function(Order $order){
            //year+num of order => 20250001
            $order->number=Order::getNextOrderNumber();
        });
    }

    public  static function getNextOrderNumber(){
        //SELECT MAX(number) from oredrs
        $year=Carbon::Now()->year;
        $number=Order::whereYear('created_at',$year)->max('number');
        if($number){
            return $number +1;
        }
        return $year.'0001';
    }
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            "name"=>'Guset Customer'
        ]);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class,'order_items','order_id','product_id','id','id')
        ->using(OrderItems::class)
        ->withPivot([
            'product_name','price','quantity','options'
        ]);//pivot table
    }

    public function addresses(){
        return $this->hasMany(OrderAddress::class);
    }

    public function billingAddress(){
        return $this->hasOne(OrderAddress::class,'order_id','id')
        ->where('type','billing');
    }

    public function shippingAddress(){
        return $this->hasOne(OrderAddress::class,'order_id','id')
        ->where('type','shipping');
    }
}
