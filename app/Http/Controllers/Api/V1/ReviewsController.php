<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewsRequest;
use App\Http\Requests\UpdateReviewsRequest;
use App\Models\Product;
use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewsController extends Controller
{
    public function index($productId){
        $product=Product::with('reviews.user')->findOrFail($productId);
        if($product){
            return response()->json([
                'status'=>true,
                "reviews"=>$product->reviews,
                "message"=>'Reviews fetched successfully',
            ],200);
        }
        return response()->json([
            'status'=>false,
            "message"=>'Reviews not found',
        ],status: 404);
    }


    public function store(StoreReviewsRequest $request,$productId){
        $request->validated();
        $product=Product::findOrFail($productId);
        
        //updateOrCreate => to make user can't do more than one reviews
        $review=Reviews::updateOrCreate([
            [
                'user_id'=>Auth::user()->id,
                'product_id'=>$product->id,
            ],
            [
                'rating'=>$request->rating,
                'comment'=>$request->comment,
            ],
        ]);

        return response()->json([
            'status'=>true,
            'review'=>$review,
            "message"=>'review created successfully',
        
        ],201);
    }

    public function update(UpdateReviewsRequest $request,$productId){
        $userId=Auth::user()->id;
        $review=Reviews::where('id',$userId)
        ->where('product_id',$productId)->findOrFail();
        $validated=$request->validated();
        $review->update($validated);
        // $review->update($request->only([
        //     'rating',
        //     'comment',
        // ]));

        return response()->json([
            'status'=>true,
            'review'=>$review,
            "message"=>'review updated successfully',
        
        ],200);
    }

    public function destroy(Request $request ,$id){
        $userId=Auth::user()->id;
        $review=Reviews::where('id',$userId)
        ->where('product_id',$id)->findOrFail();
        $review->delete();

        return response()->json([
            'status'=>true,
            "message"=>'review deleted successfully',
        
        ],200);
    }
}
