<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Repostries\cart\CartRepositry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CartRepositry $cart)
    {
        // $repositry=App::make('cart');
        $items=$cart->get();
        return response()->json([
            "message"=>'item in cart fetched successfully',
            "Cart"=>$items,
            "total"=>$cart->total(),
        ],200);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartRequest $request,CartRepositry $cart)
    {
        $request->validated();
        $product=Product::findOrFail($request->product_id);
        // $repositry=App::make('cart');

        $item=$cart->add($product,$request->quantity);
        return response()->json([
            'message' => 'Product added to cart successfully',
            'cart_item' => $item,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartRequest $request, $id, CartRepositry $cart)
    {
        $validated = $request->validated();
        $item = $cart->update($id, $validated['quantity']);
        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }
    
        return response()->json([
            'message' => 'Item updated successfully',
            'cart_item' => $item,
        ], 200);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartRepositry $cart,$id)
    {
        // $repositry=App::make('cart');
        $item =Cart::find($id);
        if (!$item) {
            return response()->json([
                'message' => 'Product not found in cart',
            ], 404);
        }
        $cart->delete($id);
        return response()->json([
            'message' => 'Product deleted successfully',
        ], 200);
    }



}
