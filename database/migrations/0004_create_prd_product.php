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
        Schema::create('prd_product', function (Blueprint $table) {
            $table->id();
            $table->string('ean', Constants::EAN_MAX_LENGTH)->unique();
            $table->string('name', 200);
            $table->string('image_url', Constants::URL_MAX_LENGTH)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prd_product');
    }
};
