<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name','slug','short_description','description',
        'regular_price','sale_price','SKU',
        'stock_status','quantity','featured','image',
        'status','category_id','brand_id'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function brand(){
        return $this->belongsTo(brands::class);
    }

    public function reviews(){
        return $this->hasMany(Reviews::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItems::class);
    }

    public function seller(){
        return $this->belongsTo(User::class,'seller_id');
    }

    public function wishliedBy(){
        return $this->belongsToMany(User::class,'wishlists','product_user','product_id','user_id')->withTimestamps();
    }
}
