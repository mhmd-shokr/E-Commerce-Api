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
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
        // Customer Info
        $table->enum('type',['billing','shipping']);
            $table->string('name');
        $table->string('email');
        $table->string('phone')->nullable();
        $table->string('locality');
        $table->string('address');
        $table->string('city');
        $table->string('state');
        $table->char('country');
        $table->string('landmark')->nullable();
        $table->string('postal_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
    }
};
