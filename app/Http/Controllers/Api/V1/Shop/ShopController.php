<?php

namespace App\Http\Controllers\Api\v1\Shop;

use App\filters\v1\CategoryFilter;
use App\filters\v1\ProductFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\v1\CategoryResource;
use App\Http\Resources\Admin\v1\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function getAllProducts(Request $request)
    {
        $filter = new ProductFilter();
        $queryItems = $filter->transform($request); // [['column','operator','value'], ...]
    
        $query = Product::query();
    
        if (count($queryItems) > 0) {
            foreach ($queryItems as $condition) {
                $query->where(...$condition);
            }
        }
        // SELECT * FROM products WHERE sale_price < 1200 AND category_id = 3;
        return ProductResource::collection($query->paginate());
    }
    
    public function getAllCategories(Request $request)
    {
        $filter = new CategoryFilter();
        $queryItems = $filter->transform($request);
    
        $query = Category::query();
    
        if (count($queryItems) > 0) {
            foreach ($queryItems as $condition) {
                $query->where(...$condition);
            }
        }
    
        return CategoryResource::collection($query->paginate());
    }
    
    public function productDetails($product_slug){
        $product = Product::where('status','active')
                        ->where('slug',$product_slug)
                        ->firstOrFail();

        $related = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->take(6)
        ->get();

        return response()->json([
            'message'          => 'success',
            'product'          => $product,
            'related_products' => $related,
        ]); 
    }
}
