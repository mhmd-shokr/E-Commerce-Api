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
        Schema::create('Order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                // Pricing
            $table->decimal('subtotal',8,2);
            $table->decimal('discount',8,2)->default(0);
            $table->decimal('tax',8,2);
            $table->decimal('total',8,2);

            $table->string('payment_method');
            $table->string('number')->unique();
            // Order Status
            $table->enum('status',['pending','canceled','delivered','proccesing','refunded']);
            $table->enum('payment_status',['pending','paid','failed']);
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
        Schema::dropIfExists('Order');
    }
};
