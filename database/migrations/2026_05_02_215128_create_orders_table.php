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
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();

            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            $table->string('shipping_full_name');
            $table->string('shipping_phone');
            $table->string('shipping_country')->default('US');
            $table->string('shipping_state')->nullable();
            $table->string('shipping_city');
            $table->string('shipping_postal_code');
            $table->string('shipping_address_line_one');
            $table->string('shipping_address_line_two')->nullable();

            $table->enum('payment_method', ['stripe','cash'])->default('stripe');
            $table->enum('payment_status', ['pending','paid','failed','refunded'])->default('pending');
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending','processing','shipped','delivered','cancelled'])->default('pending');
            $table->string('tracking_number')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
