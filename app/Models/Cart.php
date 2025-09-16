<?php

namespace App\Models;

use App\Observers\CartObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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

            static::addGlobalScope('cookie_id',function(Builder $builder){
                if (!Auth::check()) {
                    $builder->where('cookie_id', '=', Cart::getCookieId());
                }
            });
        }

        public static function getCookieId(){
            //if user guest
            $cookie_id=Cookie::get('cart_id');
            if(!$cookie_id){
                $cookie_id=Str::uuid();
                Cookie::queue('cart_id', $cookie_id, 30*24*60);
            }
            return $cookie_id;
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
