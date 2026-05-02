<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Module\Shared\Constants;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ofr_offer', function (Blueprint $table) {
            $table->id();
            $table->integer('version')->default(0);
            $table->foreignId('store_id');
            $table->string('external_id', Constants::OFFER_EXTERNAL_ID_MAX_LENGTH);
            $table->string('product_ean', Constants::EAN_MAX_LENGTH);
            $table->string('product_name', 200)->nullable();
            $table->integer('price');
            $table->integer('lowest_price')->nullable();
            $table->boolean('visible')->default(true);
            $table->foreignId('product_id')->nullable();
            $table->timestamps();

            $table->unique(['store_id', 'product_ean']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ofr_offer');
    }
};
