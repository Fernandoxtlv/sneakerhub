<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique(); // e.g., SNEAK-0001
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status', 30)->default('pending');
            // Status: pending, confirmed, paid, processing, shipped, delivered, completed, cancelled, refunded
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(18); // IGV percentage
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('payment_method', 30)->nullable(); // cash, yape
            $table->string('payment_status', 30)->default('pending'); // pending, paid, failed, refunded
            $table->json('shipping_address')->nullable(); // {name, address, city, phone, reference}
            $table->json('billing_address')->nullable();
            $table->text('notes')->nullable();
            $table->string('coupon_code', 50)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index('order_number');
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('payment_status');
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
