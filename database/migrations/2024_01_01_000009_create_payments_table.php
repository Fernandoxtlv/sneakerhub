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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('payment_method', 30); // cash, yape
            $table->decimal('amount', 12, 2);
            $table->string('currency', 5)->default('PEN');
            $table->string('status', 30)->default('pending'); // pending, completed, failed, refunded
            $table->string('transaction_id')->nullable(); // External transaction reference
            $table->string('yape_reference')->nullable(); // Yape specific reference
            $table->string('yape_phone', 20)->nullable();
            $table->json('metadata')->nullable(); // Additional payment data
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('transaction_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
