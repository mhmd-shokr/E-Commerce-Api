<?php
namespace App\Repostries\cart;

use App\Models\Cart;
use App\Models\Product;
use Collator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class CartRepositry implements CartRepositryInterface{
    public function get():Collection
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->get();
        }
        return Cart::where('cookie_id','=',$this->getCookieId())->get();
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

        $item=Cart::where('product_id','=',$product->id)
        ->where('cookie_id','=',$this->getCookieId())->first();
        if(!$item){
            return Cart::create([
                'cookie_id'=>$this->getCookieId(),
                "user_id"=>Auth::id(),
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
        } else {
            $query->where('cookie_id', $this->getCookieId());
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
        } else {
            $query->where('cookie_id', $this->getCookieId());
        }

        return $query->delete();
            }

    public function empty(){
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->delete();
        } else {
            Cart::where('cookie_id', $this->getCookieId())->delete();
        }
    }
    public function total(): float
    {
        $query = Cart::join('products', 'products.id', '=', 'carts.product_id')
            ->where(function($q) {
                if (Auth::check()) {
                    $q->where('user_id', Auth::id());
                } else {
                    $q->where('cookie_id', $this->getCookieId());
                }
            })
            ->selectRaw('SUM(products.sale_price * carts.quantity) as total')
            ->first(); // نجيب أول نتيجة بدل value
    
        // total موجودة داخل first() كـ property
        $total = $query->total ?? 0.0;
    
        return (float) $total;
    }
    
        protected function getCookieId(){
        //if user guest
        $cookie_id=Cookie::get('cart_id');
        if(!$cookie_id){
            $cookie_id=Str::uuid();
            Cookie::queue('cart_id', $cookie_id, 30*24*60);
        }
        return $cookie_id;
    }

}