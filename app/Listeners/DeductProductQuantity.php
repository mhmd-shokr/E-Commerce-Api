<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeductProductQuantity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        // $items=Cart::get();
        // foreach($items as $item){
        //     Product::where('id',$item->product_id)
        //     ->update([
        //         'quantity'=>DB::raw("quantity - {$item->quantity}"),
        //     ]);
        // }

        $order=$event->order;
        foreach($order->products as $product){
            $product->decrement('quantity'.$product->pivot->quantity);//decrement('name of column,what you do)
        }

    }
}
