<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Slide;
use Illuminate\Support\Facades\Request;

class HomeController extends Controller
{
    
    public function index(Request $request)
    {
        $slides=Slide::where('status',1)->take(3)->get();
        $categories=Category::orderBy('name')->get();
        return response()->json([
            "message"=>'data returned succesfully',
            'slides'=>$slides,
            "category"=>$categories
        ],200);
    }
}
