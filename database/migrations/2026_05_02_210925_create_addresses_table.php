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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('phone');
            $table->string('country')->default('US');
            $table->string('state')->nullable();
            $table->string('city');
            $table->string('postal_code');
            $table->string('address_line_one');
            $table->string('address_line_two')->nullable();
            $table->boolean('is_default')->default(false);
            $table->enum('type', ['shipping','billing','both'])->default('shipping');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
