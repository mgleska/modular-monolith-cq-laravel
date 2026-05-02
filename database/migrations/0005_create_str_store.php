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
        Schema::create('str_store', function (Blueprint $table) {
            $table->id();
            $table->string('external_id', Constants::STORE_EXTERNAL_ID_MAX_LENGTH)->unique();
            $table->string('name', 200);
            $table->string('address', 200);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('str_store');
    }
};
