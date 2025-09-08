<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                // Pricing
            $table->decimal('subtotal',8,2);
            $table->decimal('discount',8,2)->default(0);
            $table->decimal('tax',8,2);
            $table->decimal('total',8,2);
                // Customer Info
            $table->string('name');
            $table->string('phone');
            $table->string('locality');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('landmark')->nullable();
            $table->string('zip');
            $table->enum('type',['home','work'])->default('home');
            // Order Status
            $table->enum('status',['ordered','canceled','delivered']);
            $table->boolean('is_shipping_different')->default(false);
                // Dates
            $table->date('delivered_date')->nullable();
            $table->date('canceled_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
