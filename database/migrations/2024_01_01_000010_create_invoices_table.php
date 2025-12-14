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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number', 30)->unique(); // e.g., BOL-0001
            $table->string('type', 20)->default('boleta'); // boleta, factura
            $table->string('pdf_path')->nullable();
            $table->string('ruc', 15)->nullable(); // For facturas
            $table->string('business_name')->nullable(); // Razón social for facturas
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('total', 12, 2);
            $table->timestamp('issued_at');
            $table->timestamps();

            $table->index('invoice_number');
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
