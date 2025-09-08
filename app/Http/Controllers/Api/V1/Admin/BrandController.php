<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\v1\StoreBrandRequest;
use App\Http\Requests\Admin\v1\UpdateBrandRequest;
use App\Http\Resources\Admin\v1\BrandResource;
use App\Models\brands;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(Request $request){
        $brands=brands::orderBy('id','desc')->paginate(10);
        return response()->json([
            'data'=> BrandResource::collection ($brands),
            "message"=>'success',
        ]);
    }

    public function store(StoreBrandRequest $request){
        $imageName=Null;
        if($request->hasFile('image')){
            $destinationPath = storage_path('app/public/brands');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $originalName=pathinfo($request->image->getClientOriginalName(),PATHINFO_FILENAME);
            $extension=$request->image->getClientOriginalExtension();
            $imageName = Str::slug($originalName) . '-' . time() . '.' . $extension;
            $request->image->move($destinationPath, $imageName);
        }
        
        $brand=brands::create([
            'name'=>$request->name,
            'slug'=>Str::slug($request->slug),
            'status'=>$request->status,
            'image' => $imageName,
        ]);
        return  response()->json([
            "data"=>new BrandResource($brand),
            "message"=>'Brand saved success',
        ],201);
    }

    public function update(UpdateBrandRequest $request,brands $brand){
        $request->validated();
        $imageName=$brand->image;
        if($request->hasFile('image')){
            $destinationPath = storage_path('app/public/brands');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            if ($imageName && file_exists($destinationPath . '/' . $imageName)) {
                unlink($destinationPath . '/' . $imageName);
            }
            $originalName=pathinfo($request->image->getClientOriginalName(),PATHINFO_FILENAME);
            $extension=$request->image->getClientOriginalExtension();
            $imageName = Str::slug($originalName) . '-' . time() . '.' . $extension;
            $request->image->move($destinationPath, $imageName);
        }
        
        $brand->update([
            'name'=>$request->name,
            'slug'=>Str::slug($request->slug),
            'status'=>$request->status,
            'image' => $imageName,
        ]);
        return  response()->json([
            "data"=>new BrandResource($brand),
            "message"=>'Brand updated success',
        ],200);

    }


    public function destroy(brands $brand)
    {
        $destinationPath = storage_path('app/public/brands');
        $imagePath=$destinationPath.$brand->image;
        if (!empty($brand->image) && file_exists($imagePath) && is_file($imagePath)) {
            unlink($imagePath);
        }
        $brand->delete();
        return response()->json([
            "message"=>'Brand deleted successfully',
        ],200);
    }    

}
