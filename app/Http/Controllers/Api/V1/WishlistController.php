<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request){
        $wishlists=$request->user()->wishlist()->with('brand','category')->get();
        return response()->json([
            'message'=>'your wishlist success',
            'wishlist'=>$wishlists,
        ],200);
    }

    public function store(Request $request,$productId){
        $product=Product::findOrFail($productId);
        $request->user()->wishlist()->syncWithoutDetaching([$product->id]);
        return response()->json([
            'status'=>true,
            'message'=>'Product add to wishlist',
        ],201);
    }

    public function destroy(Request $request,$productId){
        $request->user()->wishlist()->detach($productId);
        return response()->json([
            'status'=>true,
            'message'=>'Product deleted from wishlist',
        ],200);
    }
}
