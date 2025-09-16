<?php

namespace App\Providers;

use App\Listeners\DeductProductQuantity;
use App\Listeners\EmptyCart;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    protected $listen=[
        'oredr_created'=>[
            DeductProductQuantity::class,
            EmptyCart::class,
        ]
    ];
    public function register(): void
    {
    
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
