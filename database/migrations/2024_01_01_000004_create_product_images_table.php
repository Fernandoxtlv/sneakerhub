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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('filename'); // Original filename
            $table->string('path'); // Full path in storage
            $table->string('path_thumb')->nullable(); // Thumbnail 200px
            $table->string('path_medium')->nullable(); // Medium 800px
            $table->string('alt_text')->nullable();
            $table->boolean('is_main')->default(false);
            $table->integer('position')->default(0);
            $table->string('mime_type', 50)->nullable();
            $table->integer('file_size')->nullable(); // In bytes
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'is_main']);
            $table->index(['product_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
