<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\v1\UpdateOrderRequest;
use App\Http\Resources\Admin\v1\OrderItemsResource;
use App\Http\Resources\Admin\v1\OrderResource;
use App\Models\OrderItems;
use App\Models\Orders;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;

class OrderController extends Controller
{
    public function index(Request $request){
        $orders=Orders::orderBy('created_at','desc')->paginate();
        return response()->json([
            "data"=>OrderResource::collection($orders),
            "message"=>'orders fetched successfully',
        ],200);
    }

    public function show(Orders $order){
        // $transaction=Transaction::where('order_id',$order->id)->first();
        return response()->json([
            "message"=>'orders fetched successfully',
            "order"=>new OrderResource($order->load('OrderItems')),
            // "transaction"=>$transaction,
        ],200);
    }

    public function update(UpdateOrderRequest $request,Orders $order){
        $request->validated(); //change status debend on user request delivered or canceled or ordered
        $order->status = $request->order_status;
    
        if ($request->order_status == 'delivered') {
            $order->delivered_date = Carbon::now();
            
        } elseif ($request->order_status == 'canceled') {
            $order->canceled_date = Carbon::now();
        }
        $order->save();

        // if ($request->order_status == 'delivered') {
        //     $transaction = Transaction::where('order_id', $order->id)->first();
        //     if ($transaction) {
        //         $transaction->status = 'approved';
        //         $transaction->save();
        //     }
        // }
        return response()->json([
            "message"=>'Status changed successfully',
            "data"=>new OrderResource($order),
        ],200);
    }

}
