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
        Schema::create('account_rate_disallowedin', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')->constrained('core_country')->onDelete('cascade')->index('account_rate_disallowedin_country_id');
            $table->foreignId('rate_id')->constrained('account_rate')->onDelete('cascade')->index('account_rate_disallowedin_rate_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_rate_disallowedin');
    }
};
