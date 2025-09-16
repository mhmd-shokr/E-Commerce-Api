<?php
namespace App\Repostries\cart;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class CartRepositry implements CartRepositryInterface{
    public function get(): Collection
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->get();
        }
    
        // Guest user => filter by cookie_id
        return Cart::where('cookie_id', Cart::getCookieId())->get();
    }
    

    public function add(Product $product,$quantity=1){
        
        if (Auth::check()) {
            $item = Cart::where('product_id', $product->id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$item) {
                return Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);
            }

            $item->increment('quantity', $quantity);
            return $item;
        }
        //if guest
        $item=Cart::where('product_id','=',$product->id)->first();
        if(!$item){
            return Cart::create([
                'cookie_id' => Cart::getCookieId(),
                "user_id"=>null,
                "product_id"=>$product->id,
                "quantity"=>$quantity,
            ]);
        }
        return $item->increment('quantity',$quantity);
    }
    public function update($id, $quantity)
    {
        $query = Cart::where('id', $id);

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } 
        $cartItem = $query->first();
        if (!$cartItem) {
            return null; 
        }
    
        $cartItem->quantity = $quantity;
        $cartItem->save();
        return $cartItem; 
    }
    
    

        public function delete($id){
            $query = Cart::where('id', $id);
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } 
        return $query->delete();
            }

            public function empty()
            {
                if (Auth::check()) {
                    Cart::where('user_id', Auth::id())->delete();
                } else {
                    Cart::where('cookie_id', Cart::getCookieId())->delete();
                }
            }
            
    public function total(): float
        {
            $query = Cart::join('products', 'products.id', '=', 'carts.product_id')
                ->selectRaw('SUM(COALESCE(products.sale_price,products.regular_price) * carts.quantity) as total');

            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            }else{
                $query->where('carts.cookie_id',Cart::getCookieId());
            }

            $result = $query->first(); 
            return (float) ($result->total ?? 0.0);
        }

    public function apply(Request $request,CartRepositry $cart)
        {
            $validated=$request->validate(['code'=>'required']);
            $code=strtoupper($validated['code']);
            $coupon=Coupon::where('code',$code)
            ->whereDate('expiry_date','>=',Carbon::today())->first();
            if(!$coupon){
                return response()->json(["message"=>'Invalid Or Expired Coupon'],400);
            }
            $cartTotal=$cart->total();
            if($cartTotal<(float) $coupon->cart_value){
                return response()->json(["message"=>"Cart Total Must Be At Least {$coupon->cart_value}"],400);
            }
            if($coupon->type === 'fixed'){
                $discount=(float) $coupon->value;
            }else{
                $discount = round($cartTotal * ((float)$coupon->value / 100),2);
            }

            if($discount>$cartTotal)
            $discount=$cartTotal;
            $newTotal=round($cartTotal-$discount,2);
                return response()->json([
                    "message"=>'Coupon applied successfully',
                    'data'=>[
                    'coupon'=>$coupon,
                    'cart_total'=>$cartTotal,
                    'discount'=>$discount,
                    'new_total'=>$newTotal,
                    ]
                ],200);
        }

}
