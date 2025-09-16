<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\v1\OrderResource;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\SellerRequest;
use App\Models\User;
use App\Notifications\SellerRequestNotification;
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

    public function Order(){
        $Order=Order::where('user_id',Auth::id())->orderBy('created_at','desc')->paginate(10);
        return response()->json([
            'message' => 'success',
            'Order' => $Order
        ]);
    }

    public function orderDetails($orderId)
    {
        $order=Order::where('user_id',Auth::id())->where('id',$orderId)->first();
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

    public function cancelOredr(Request $request,Order $order)
    {
        // $order=Order::findOrFail($request->order_id);
        
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


    public function requestSeller(Request $request)
        {
            $user=Auth::user();
            if($user->sellerRequest && $user->sellerRequest->status==='pending'){
                return response()->json(['message'=>'You Already Have a Pending Request']);
            }
            $sellerRequest=SellerRequest::create([
                'user_id'=>$user->id,
                'status'=>'pending',
            ]);

            $admins=User::role('Admin')->get();
            foreach($admins as $admin){
                $admin->notify(new SellerRequestNotification($sellerRequest));
            }

            return response()->json(['message'=>'Your Request Has Been Sybmitted And Witting Approve','request'=>$sellerRequest],201);
        }
}
