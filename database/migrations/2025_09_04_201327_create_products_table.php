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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
                  // Basic product info
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable(); 
            $table->text('description')->nullable();
                    // Pricing
            $table->decimal('regular_price', 8, 2); 
            $table->decimal('sale_price', 8, 2)->nullable(); 
            $table->string('SKU')->unique();
                    // Stock & Quantity
            $table->enum('stock_status',['instock','outofstock'])->default('instock');
            $table->unsignedInteger('quantity')->default(1);
                  // Extra info
            $table->boolean('featured')->default(false);
            $table->string('image');
                  // Status
            $table->enum('status', ['active','inactive'])->default('active');
                    //Relations
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->text('images')->nullable();
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
