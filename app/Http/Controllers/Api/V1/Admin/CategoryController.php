<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\v1\StoreCategoryRequest;
use App\Http\Requests\Admin\v1\UpdateCategoryRequest;
use App\Http\Resources\Admin\v1\CategoryResource;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request){
        $categories=Category::with('products')->withCount('products')->get();
        return response()->json([
            "data"=> CategoryResource::collection($categories),
            'message'=>'Success'
        ],200);
    }
    
    public function store(StoreCategoryRequest $request){
        $validated=$request->validated();
        $imageName=null;
        if($request->hasFile('image')){
            $destinationPath=storage_path('app/public/categories');
            if(!File::exists($destinationPath)){
                mkdir($destinationPath, 0755, true);
            }
            // save fileName without extension 
            $originalName = pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
            //save extension 
            $extension = $request->image->getClientOriginalExtension();
             //compine fileName + extension
            $imageName = Str::slug($originalName) . '-' . time() . '.' . $extension;
            $request->image->move($destinationPath, $imageName);
        }
        $category=Category::create([
            'name'=>$request->name,
            'slug'=> $request->slug ? Str::slug($request->slug) : Str::slug($request->name),
            'image' => $imageName,
        ]);
        return response()->json([
            'message'=>'category created successfully',
        ],201);
    }
    public function update(UpdateCategoryRequest $request,Category $category){
        $imageName = $category->image;
        
        if($request->hasFile('image')){
            // path of file will save in
            $destinationPath=storage_path('app/public/categories');
    
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
    
            // delete old image if exists
            if ($imageName && file_exists($destinationPath . '/' . $imageName)) {
                unlink($destinationPath . '/' . $imageName);
            }
    
            // save fileName without extension 
            $originalName = pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
            // save extension 
            $extension = $request->image->getClientOriginalExtension();
            // combine fileName + timestamp + extension
            $imageName = Str::slug($originalName) . '-' . time() . '.' . $extension;
            $request->image->move($destinationPath, $imageName);
        }
    
        $category->update([
            'name'=>$request->name ?? $category->name,
            'slug'=> $request->slug ? Str::slug($request->slug) : Str::slug($request->name),
            'image' => $imageName,
        ]);
    return response()->json([
        'message'=>'category updated successfully',
        "data"=>$category,
    ],200);
}

    public function destroy(Category $category){
        // path of file will save in
        $destinationPath=storage_path('app/public/categories');
        $imagePath = $destinationPath . $category->image;
        if (!empty($category->image) && file_exists($imagePath) && is_file($imagePath)) {
            unlink($imagePath);
        }
        
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ], 200);
    }
}
