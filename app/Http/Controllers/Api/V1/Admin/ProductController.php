<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\v1\ProductRequest;
use App\Http\Requests\Admin\v1\UpdateProductRequest;
use App\Http\Resources\Admin\v1\ProductResource;
use App\Http\Resources\Admin\v1\storeProductResource;
use App\Http\Resources\Admin\v1\UpdateProductResource;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
// use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products=Product::with(['category','brand'])->orderBy('created_at','desc')->paginate(10);
        return response()->json([
            "data"=>ProductResource::collection($products),
            "message"=>'success',
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $validated = $request->validated();
    
        $product = new Product();
        $product->name = $request->name;
        $product->slug = trim(Str::slug($request->slug));
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = Str::upper(Str::slug($request->SKU, '-'));
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured ?? false;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
    
        $current_timeTemp = Carbon::now()->timestamp;
        
        $gallary_arr = [];
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timeTemp . '_main.' . $image->extension();
            $path = storage_path('app/public/products');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
            $image->move($path, $imageName); 
            $product->image = $imageName;  
        }
        
    
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $counter = 1;
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                if (in_array(strtolower($gextension), ['jpg', 'jpeg', 'png', 'webp'])) {
                    $gfileName = $current_timeTemp . '_' . $counter . '.' . $gextension;
                    $path = storage_path('app/public/products/gallary');
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0755, true);
                    }
                    $file->move($path, $gfileName);
                    $gallary_arr[] = $gfileName;
                    $counter++;
                }
            }
        }
        $product->images = implode(',', $gallary_arr);
        $product->save();
        return response()->json([
            'message' => 'Product created successfully',
            'data' => new storeProductResource( $product)
        ], 201);
    }
    

    /**
     * Display the specified resource.
     */
    // public function show($id)
    // {
    //     $product= Product::findOrFail($id);

    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request,Product $product)
    {
        $validated = $request->validated();

        // $product=Product::findOrFail($id);
        $product->name = $request->name;
        $product->slug = $request->slug ? trim(Str::slug($request->slug)) : Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = Str::upper(Str::slug($request->SKU, '-'));
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured ?? false;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
    
        $current_timeTemp = Carbon::now()->timestamp;
        
        $gallary_arr = [];
        if($request->hasFile('image')){
            if($product->image&&File::exists(storage_path('app/public/products/').$product->image))    {
                File::delete(storage_path('app/public/products/'.$product->image));
            }  
                $image = $request->file('image');
                $imageName = $current_timeTemp . '_main.' . $image->extension();
                $path = storage_path('app/public/products');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true);
                }
                $image->move($path, $imageName); 
                $product->image = $imageName;  
        }
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            if($product->images){
                $oldImages=explode(',',$product->images);
                foreach ($oldImages as $oldImage) {
                    $oldImagePath = storage_path('app/public/products/gallary/'.$oldImage);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }
            }
                $counter = 1;
                foreach ($files as $file) {
                    $gextension = $file->getClientOriginalExtension();
                    if (in_array(strtolower($gextension), ['jpg', 'jpeg', 'png', 'webp'])) {
                        $gfileName = $current_timeTemp . '_' . $counter . '.' . $gextension;
                        $path = storage_path('app/public/products/gallary');
                        if (!File::exists($path)) {
                            File::makeDirectory($path, 0755, true);
                        }
                        $file->move($path, $gfileName);
                        $gallary_arr[] = $gfileName;
                        $counter++;
                    }
                }
        }
        if (!empty($gallary_arr)) {
            $product->images = implode(',', $gallary_arr);
        }
        $product->save();
        return response()->json([
            'message' => 'Product Updated successfully',
            'data' => new UpdateProductResource( $product)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        $product= Product::findOrFail($id);
        if(File::exists(storage_path('app/public/products/').$product->image)){
            File::delete(storage_path('app/public/products/').$product->image);
        }

        if(File::exists(storage_path('app/public/products/gallary/').$product->image)){
            File::delete(storage_path('app/public/products/gallary/').$product->image);
        }

        foreach(explode(',',$product->images) as $oFile){
            if(File::exists(storage_path('app/public/products/').$product->image)){
                File::delete(storage_path('app/public/products/').$product->image);
            }
    
            if(File::exists(storage_path('app/public/products/gallary/').$product->image)){
                File::delete(storage_path('app/public/products/gallary/').$product->image);
            }
        }

        $product->delete();
        return response()->json([
            "message"=>'Product deleted successfully',
        ],200);
    }
}
