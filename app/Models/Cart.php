<?php

namespace App\Models;

use App\Observers\CartObserver;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $keyType = 'string';

    public $incrementing=false;
        protected $fillable = ['user_id','cookie_id','product_id','quantity','options'];


        protected static function booted(){
            // 1: the first Way 
            // static::creating(function(Cart $cart){
            //     $cart->id=Str::uuid();
            // });
// --------------------------------------------------------------------
            //2:the second way
            static::observe(CartObserver::class);
        }
    public function user(){
        return $this->belongsTo(User::class)->withDefault([
            'name'=>'Anonymous',
        ]);  
    }

    public function produst(){
        return $this->belongsTo(Product::class);
    }
}
