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
        Schema::create('account_gateway_allowedin', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')->constrained('core_country')->onDelete('cascade')->index('country_id');
            $table->foreignId('gateway_id')->constrained('account_gateway')->onDelete('cascade')->index('gateway_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_gateway_allowedin');
    }
};
