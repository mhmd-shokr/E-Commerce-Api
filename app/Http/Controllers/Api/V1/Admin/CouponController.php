<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\v1\StoreCouponRequest;
use App\Http\Requests\Admin\v1\UpdateCouponRequest;
use App\Http\Resources\Admin\v1\CouponResource;
use App\Models\Coupon;
use Illuminate\Support\Facades\Request;
use index;
use SebastianBergmann\CodeUnit\CodeUnitCollection;

class CouponController extends Controller
{
    public function index(Request $request){
        $Coupon=Coupon::orderBy('expiry_date','desc')->paginate(12);
        return response()->json([
            "message"=>'success',
            "data"=>$Coupon
        ],200);
    }

    public function store(StoreCouponRequest $request){
        $request->validated();
        $data= Coupon::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'cart_value' => $request->cart_value,
            'expiry_date' => $request->expiry_date,
        ]);
        return response()->json([
            "message"=>'Coupon Created Successfully',
            "data"=>new CouponResource($data)
        ],201);
    }
    public function update(UpdateCouponRequest $request,Coupon $coupon){
        $validated = $request->validated();
            $coupon->update([
                'code' => $request->code ? strtoupper($request->code) : $coupon->code,
                'type' => $request->type ?? $coupon->type,
                'value' => $request->value ?? $coupon->value,
                'cart_value' => $request->cart_value ?? $coupon->cart_value,
                'expiry_date' => $request->expiry_date ?? $coupon->expiry_date,
        ]);
        return response()->json([
            "message"=>'Coupon updated Successfully',
            "data"=>new CouponResource($coupon)
        ],200);  
    }

    public function destroy(Coupon $coupon){
        $coupon->delete(); return response()->json([
            "message"=>'Coupon Deleted Successfully',
        ],200);  
    }
}
