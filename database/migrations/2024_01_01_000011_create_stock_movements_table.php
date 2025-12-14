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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity'); // Positive for increase, negative for decrease
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('type', 30); // sale, purchase, adjustment, return, damage
            $table->string('reason')->nullable();
            $table->string('reference_type')->nullable(); // App\Models\Order
            $table->unsignedBigInteger('reference_id')->nullable(); // Order ID, etc.
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index('product_id');
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
