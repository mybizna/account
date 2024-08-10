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
        Schema::create('account_gateway_rate', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gateway_id')->constrained('account_gateway')->onDelete('cascade')->index('account_gateway_rate_gateway_id');
            $table->foreignId('rate_id')->constrained('account_rate')->onDelete('cascade')->index('account_gateway_rate_rate_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_gateway_rate');
    }
};
