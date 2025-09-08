<?php

use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\Admin\BrandController;
use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\CouponController;
use App\Http\Controllers\Api\v1\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\ProductController;
use App\Http\Controllers\Api\V1\Admin\SlideController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\v1\Shop\ShopController;

use App\Http\Controllers\Api\v1\User\DashboardController as UserDashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::group(['prefix'=>'v1'],function(){

    Route::Post('/register',[AuthController::class,'register']);
    Route::Post('/login',[AuthController::class,'login']);
    Route::get('/', [HomeController::class, 'index'])->name('home.index');

    //seacrh or filter
    Route::get('products/{slug}',[ShopController::class,'productDetails']);
    Route::get('getAllProducts',[ShopController::class,'getAllProducts']);
    Route::get('getAllCategories',[ShopController::class,'getAllCategories']);

  






    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/logout', [AuthController::class, 'logout']);
        Route::get('/profile',    [AuthController::class, 'profile']);
        Route::get('/refresh-token',    [AuthController::class, 'refreshToken']);
          //cart
    Route::apiResource('cart',CartController::class);
    });

    //Admin
    Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
        Route::post('/users/{id}/change-role', [AdminController::class, 'changeRole']);
        Route::post('/admin/dashboard', [AdminDashboardController::class, 'index']);
    });

    //admin &&seller
    Route::middleware(['auth:sanctum', 'role:Admin|Seller'])->group(function () {
        Route::apiResource('/products', ProductController::class);
        Route::apiResource('/categories', CategoryController::class);
        Route::apiResource('/brands', BrandController::class);
        Route::apiResource('/coupons', CouponController::class);
        Route::apiResource('/slides', SlideController::class);
    });

    //user
    Route::middleware(['auth:sanctum', 'role:Customer'])->group(function () {
        Route::post('/user/dashboard', [UserDashboardController::class, 'index']);
    });

});
