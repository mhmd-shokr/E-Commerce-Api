<?php

namespace App\Http\Controllers\Api\V1\Admin;

use  App\Http\Controllers\Controller;
use App\Http\Requests\storeSlideRequest;
use App\Http\Requests\UpdateSlideRequest;
use App\Models\Slide;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
    public function index(Request $request){
        $slides=Slide::orderBy('id','desc')->paginate(12);
        return response()->json([
            "data"=>$slides,
            "message"=>'success',
        ],200);
    }

    public function store(storeSlideRequest $request){
        $request->validated();

        $slide=new Slide();
        $slide->tagline=$request->tagline;
        $slide->title=$request->title;
        $slide->subtitle=$request->subtitle;
        $slide->link=$request->link;
        $slide->status=$request->status;

        if($request->hasFile('image')){
            $path = $request->file('image')->store('slides', 'public');
            $slide->image = $path;
        }
        $slide->save();

        return response()->json([
            "data"=>$slide,
            "message"=>'Slide created successfully',
        ],201);

    }
    public function update(UpdateSlideRequest $request,Slide $slide){

        $request->validated();
        $slide->tagline = $request->tagline ?? $slide->tagline;
        $slide->title = $request->title ?? $slide->title;
        $slide->subtitle = $request->subtitle ?? $slide->subtitle;
        $slide->link = $request->link ?? $slide->link;
        $slide->status = $request->status ?? $slide->status;
        

        if($request->hasFile('image')){
            if ($slide->image) {
                $oldImagePath = storage_path('app/public/' . $slide->image);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }
            
            $path = $request->file('image')->store('slides', 'public');
            $slide->image = $path;
        }
        $slide->save();
        return response()->json([
            "data"=>$slide,
            "message"=>'Slide updated successfully',
        ],200);
    }

    public function destroy($id){
        $slide = Slide::findOrFail($id);
        $imagePath = storage_path('app/public/' . $slide->image);
        if(File::exists($imagePath)){
            File::delete($imagePath);
        }
        $slide->delete();
        return response()->json([
            "message" =>'Slide Deleted successfully'
        ],200);

    }
}
