<?php
declare(strict_types=1);

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
        Schema::create('prd_product_quantity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id');
            $table->foreignId('product_id');
            $table->integer('quantity');
            $table->timestamps();

            $table->unique(['store_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prd_product_quantity');
    }
};
