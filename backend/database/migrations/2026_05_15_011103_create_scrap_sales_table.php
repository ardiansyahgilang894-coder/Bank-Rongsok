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
        Schema::create('scrap_sales', function (Blueprint $table) {
            $table->id();
            $table->date('sale_date');
            $table->string('item_name');
            $table->integer('quantity');
            $table->decimal('weight_kg', 10, 2)->nullable();
            $table->decimal('price_per_unit', 12, 2);
            $table->decimal('total_price', 15, 2);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrap_sales');
    }
};
