<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\v1\OrderResource;
use App\Models\OrderItems;
use App\Models\Orders;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class DashboardController extends Controller
{
    public function index(Request $request){
        
        $user = Auth::user();

        return response()->json([
            'message' => 'success',
            'user' => $user
        ]);    }

    public function orders(){
        $orders=Orders::where('user_id',Auth::id())->orderBy('created_at','desc')->paginate(10);
        return response()->json([
            'message' => 'success',
            'orders' => $orders
        ]);
    }

    public function orderDetails($orderId)
    {
        $order=Orders::where('user_id',Auth::id())->where('id',$orderId)->first();
        if (!$order) {
            return response()->json([
                'message' => 'Order not found or not authorized'
            ], 404);
        }
        // $orderItems = OrderItems::where('order_id', $orderId)
        // ->orderBy('id')
        // ->paginate(12);
        // $transaction = Transaction::where('order_id', $orderId)->first();

        return response()->json([
            'message' => 'success',
            'order' => new OrderResource( $order->load('orderItems')),
            // 'items' => $orderItems,
            // 'transaction' => $transaction
        ]);
    }

    public function cancelOredr(Request $request,Orders $order)
    {
        // $order=Orders::findOrFail($request->order_id);
        
        if (!$order) {
            return response()->json([
                'message' => 'Order not found or not authorized'
            ], 404);
        }
        $order->status ='canceled';
        $order->canceled_date=Carbon::now();
        $order->save();

        return response()->json([
            'message' => 'Order canceled successfully',
            'order' =>  new OrderResource($order)
        ]);
    }
}
