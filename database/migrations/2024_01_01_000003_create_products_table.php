<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->longText('description')->nullable();
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('discount', 5, 2)->default(0); // Percentage
            $table->decimal('discount_price', 10, 2)->nullable(); // Calculated price after discount
            $table->integer('stock')->default(0);
            $table->json('sizes_available')->nullable(); // e.g., [38, 39, 40, 41, 42]
            $table->string('color', 50)->nullable();
            $table->string('material')->nullable();
            $table->string('gender')->nullable(); // men, women, unisex, kids
            $table->boolean('featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_new')->default(true);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->integer('sales_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for search and filtering
            $table->index(['is_active', 'featured']);
            $table->index(['category_id', 'is_active']);
            $table->index(['brand_id', 'is_active']);
            $table->index('price');
            if (DB::getDriverName() !== 'sqlite') {
                $table->fullText(['name', 'description']);
            }
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
