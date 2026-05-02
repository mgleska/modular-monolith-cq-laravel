<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Module\Customer\Action\Enum\CustomerStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cst_customer', function (Blueprint $table) {
            $table->id();
            $table->enum('status', [CustomerStatusEnum::ACTIVE, CustomerStatusEnum::INACTIVE, CustomerStatusEnum::DEACTIVATING])->default(CustomerStatusEnum::ACTIVE);
            $table->integer('selected_store')->default(0);
            $table->string('name', 200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cst_customer');
    }
};
