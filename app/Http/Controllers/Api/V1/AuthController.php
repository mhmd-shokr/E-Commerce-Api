<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\v1\AuthRequest;
use App\Http\Requests\Auth\v1\LoginRequest;
use App\Http\Resources\Auth\v1\AuthResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Cart;
use Illuminate\Support\Facades\Cookie;class AuthController extends Controller
{
    public function register(AuthRequest $request){
        $validated=$request->validated();
        $user=User::create([
            "name"=>$request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole('Customer');

        Auth::login($user); 
        $this->mergeCart($user->id);
        $token=$user->createToken('auth_token')->plainTextToken;
        return response()->json([ "messaage"=>'register successs', 'user' => new AuthResource( $user), 'token' => $token], 201);
    }

    public function login(LoginRequest $request){
        $validated=$request->validated();
        $user=User::where('email',$request->email)->first();
        if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        Auth::login($user);
        $this->mergeCart($user->id);

        $token=$user->createToken('auth_token')->plainTextToken;
        return response()->json(["messaage"=>'login successs', 'user' => new AuthResource( $user), 'token' => $token], 201);
    }

    public function profile(){
        $user=Auth::user();
        /** @var \App\Models\User $user */
        return response()->json([ 'roles' => $user->getRoleNames(),'user'=> new AuthResource( $user)],200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function refreshToken(){
        $user=Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
      /** @var \App\Models\User $user */
        $user->tokens()->delete();
        $newToken=$user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Token refreshed successfully',
            'token' => $newToken,
        ], 200);
    }

    public function mergeCart($user_id)
    {
        $cookie_id = request()->cookie('cart_id'); 
        if (!$cookie_id) {
            return;
        }
    
        $guestCartItems = Cart::where('cookie_id', $cookie_id)->get();
    
        foreach ($guestCartItems as $item) {
            $existingItem = Cart::where('user_id', $user_id)
                ->where('product_id', $item->product_id)
                ->first();
    
            if ($existingItem) {
                $existingItem->quantity += $item->quantity;
                $existingItem->save();
                $item->delete();
            } else {
                $item->update([
                    'user_id' => $user_id,
                    'cookie_id' => null,
                ]);
            }
        }
    
        Cookie::queue(Cookie::forget('cart_id'));
    }
    
    
}
    

