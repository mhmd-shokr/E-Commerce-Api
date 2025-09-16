<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItems extends Pivot
{
    protected $table='order_items';

    public $incrementing=true;
    protected $fillable = [
        'product_id','order_id','product_name','price','quantity','options'
    ];
    

    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault([
            'name'=>$this->product_name
,        ]);
    }


    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
