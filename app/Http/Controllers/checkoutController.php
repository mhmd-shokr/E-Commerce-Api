<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Coupon;
use App\Models\OrderAddress;
use App\Models\OrderItems;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\OredrCreatedNotification;
use App\Repostries\cart\CartRepositry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class checkoutController extends Controller
{
    public function store(StoreCheckoutRequest $request ,CartRepositry $cart){
        // Validate request input (payment_method, coupon_code, addr[billing, shipping])
        $validated=$request->validated();

        // Start transaction
        DB::beginTransaction();
        try{
            // 1. Get all cart items
            $cartItems=$cart->get();
            if($cartItems->isEmpty())
                return response()->json(['message'=>'Cart Is Empty'],400);

            // 2. Calculate subtotal
            $subTotal=0.0;
            foreach($cartItems as $item){
                $product=null;
                $product=$item->products??Product::findOrFail($item->product_id);

                // If product not found => rollback
                if(!$product){
                    DB::rollBack();
                    return response()->json(['message'=>"Product id : {$item->product_id} not found"],404);
                }

                // Get product price (prefer sale_price if available)
                $price=$product->sale_price ?? $product->regular_price;
                $quantity=$item->quantity;

                // Add to subtotal
                $subTotal += $price *  $quantity;

                // Check stock availability
                if(isset($product->quantity) && is_numeric($product->quantity)){
                    if($product->quantity < $quantity){
                        DB::rollBack();
                        return response()->json([
                            'message'=>"not enough stock for product {$product->name} available {$product->quantity} requested {$quantity}"
                        ],400);
                    }
                }
            }

            // 3. Apply coupon if provided
            $appliedCoupon=null;
            $discount=0.0;
            $couponCode=$request->input('coupon_code');
            if($couponCode){
                $coupon=Coupon::where('code',$couponCode)
                        ->whereDate('expiry_date','>=',Carbon::today())
                        ->first();

                if(!$coupon){
                    DB::rollBack();
                    return response()->json(['message'=>"Invalid or Expired Coupon"],400);
                }

                if($subTotal < (float)$coupon->cart_value){
                    DB::rollBack();
                    return response()->json(['message'=>"Cart total must be at least {$coupon->cart_value}"],400);
                }

                // Fixed discount OR percentage discount
                if($coupon->type === 'fixed'){
                    $discount = (float) $coupon->value;
                }else{
                    $discount=round($subTotal*((float) $coupon->value /100 ),2);
                }

                // Ensure discount does not exceed subtotal
                if($discount > $subTotal)
                    $discount=$subTotal;

                $appliedCoupon=$coupon;
            }

            // 4. Calculate tax
            $taxPercecnt=config('shop.tax_percent',0);
            $tax=round(($subTotal - $discount)*($taxPercecnt/100),2);

            // 5. Shipping (default = 0)
            $shippingFee=0.0;

            // 6. Total amount
            $total=round($subTotal - $discount + $tax + $shippingFee , 2);
                    
            // 7. Create order
            $order=Order::create([
                "user_id"=>Auth::id(),
                "subtotal"=>$subTotal,
                "total"=>$total,
                "discount"=>$discount,
                "tax"=>$tax,
                "payment_method"=>$request->payment_method??'cod',
                "coupon_id"=>$appliedCoupon?$appliedCoupon->id:null,
                "coupon_code"=>$appliedCoupon?$appliedCoupon->code:null,
                "status"=>'pending',
                "payment_status"=>$request->payment_status ==='cod' ? 'pending' :'paid',
            ]);

            $seller=$product->seller();
            if($seller){
                $seller->notify(new OredrCreatedNotification($order));
            }

            // 8. Create billing & shipping addresses
            $billing = $validated['addr']['billing'] ?? [];
            $shipping = $validated['addr']['shipping'] ?? [];

            // Billing address
            if (!empty($billing)) {
                $order->addresses()->create([
                    "type"    => "billing",
                    "name"    => $billing['name'] ?? '',
                    "email"   => $billing['email'] ?? '',
                    "phone"   => $billing['phone'] ?? '',
                    "locality"=> $billing['locality'] ?? '',
                    "address" => $billing['address'] ?? '',
                    "city"    => $billing['city'] ?? '',
                    "state"   => $billing['state'] ?? '',
                    "country" => $billing['country'] ?? '',
                    "landmark"=> $billing['landmark'] ?? null,
                    "postal_code" => $billing['postal_code'] ?? null,
                ]);
            }

            // Shipping address
            if (!empty($shipping)) {
                $order->addresses()->create([
                    "type"    => "shipping",
                    "name"    => $shipping['name'] ?? '',
                    "email"   => $shipping['email'] ?? '',
                    "phone"   => $shipping['phone'] ?? '',
                    "locality"=> $shipping['locality'] ?? '',
                    "address" => $shipping['address'] ?? '',
                    "city"    => $shipping['city'] ?? '',
                    "state"   => $shipping['state'] ?? '',
                    "country" => $shipping['country'] ?? '',
                    "landmark"=> $shipping['landmark'] ?? null,
                    "postal_code" => $shipping['postal_code'] ?? null,
                ]);
            }

            // 9. Create order items (from cart)
            foreach($cartItems as $item){
                $product=$item->products??Product::findOrFail($item->product_id);
                $price=$product->sale_price??$product->regular_price;
                $quantity=$item->quantity;
                    
                // Attach product to order with pivot data
                $order->products()->attach($product->id, [
                    'product_name'=>$product->name,
                    "price"=>$price,
                    'quantity'=>$quantity,
                    "options"=>json_encode($item->options ?? []),
                ]);

                // Reduce stock quantity
                if(isset($product->quantity)&&is_numeric($product->quantity)){
                    $product->decrement('quantity',$quantity);
                }
            }

            // 10. Commit transaction
            DB::commit();
            // 11. Empty cart
            // $cart->empty();
            
            // event('oredr_created',$order);

            event(new OrderCreated($order));



            // 12. Return success response
            return response()->json([
                "message" => "Order placed successfully",
                "order"   => $order->load(['addresses', 'products']),
            ], 201);

        }catch(Throwable $e){
            // Rollback if any error
            DB::rollBack();
            throw $e;
        }
    }
}