<?php

use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\Admin\BrandController;
use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\CouponController;
use App\Http\Controllers\Api\v1\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\ProductController;
use App\Http\Controllers\Api\V1\Admin\SlideController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ReviewsController;

use App\Http\Controllers\Api\v1\Shop\ShopController;
use App\Http\Controllers\Api\v1\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Api\V1\WishlistController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\checkoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
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
        //apply Coupon
        Route::Post('Coupon/apply',[CouponController::class,'apply']);
        // cart for guest (cookie_id)
        Route::apiResource('cart', CartController::class)->only(['index','store','update','destroy']);
        

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/logout', [AuthController::class, 'logout']);
        Route::get('/profile',    [AuthController::class, 'profile']);
        Route::get('/refresh-token',    [AuthController::class, 'refreshToken']);
        // cart for logged users
        Route::apiResource('user-cart', CartController::class)->only(['index','store','update','destroy']);

        //user seller_request
        Route::post('/user/seller-request',[UserDashboardController::class,'requestSeller']);

        //notifications
        Route::get('/notifications',[NotificationController::class,'index']);
        Route::get('/unread/notifications',[NotificationController::class,'unRead']);
        Route::get('/read/{id}/notifications',[NotificationController::class,'markAsRead']);

        //wishlist
        Route::get('/wishlist',[WishlistController::class,'index']);
        Route::post('/wishlist/{productId}/store',[WishlistController::class,'store']);
        Route::delete('/wishlist/{productId}/delete',[WishlistController::class,'destroy']);
        
        //reviews
        Route::get('/review',[ReviewsController::class,'index']);
        Route::post('/review/{productId}/store',[ReviewsController::class,'store']);
        Route::put('/review/{productId}/update',[ReviewsController::class,'update']);
        Route::delete('/review/{id}/delete',[ReviewsController::class,'destroy']);

    });


    //Admin
    Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
        Route::post('/users/{id}/change-role', [AdminController::class, 'changeRole']);
        Route::post('/admin/dashboard', [AdminDashboardController::class, 'index']);
        Route::get('/admin/seller-request',[AdminController::class,'indes']);
        Route::put('/admin/seller-request/{id}/approve',[AdminController::class,'approve']);
        Route::put('/admin/seller-request/{id}/reject',[AdminController::class,'reject']);
    });

    //admin &&seller
    Route::middleware(['auth:sanctum', 'role:Admin|Seller'])->group(function () {
        Route::apiResource('/products', ProductController::class);
        Route::apiResource('/categories', CategoryController::class);
        Route::apiResource('/brands', BrandController::class);
        Route::apiResource('/Coupon', CouponController::class);
        Route::apiResource('/slides', SlideController::class);
    });

    //user
    Route::middleware(['auth:sanctum', 'role:Customer'])->group(function () {
        Route::post('/user/dashboard', [UserDashboardController::class, 'index']);
        Route::post('/checkout', [checkoutController::class, 'store']);
    });

});
