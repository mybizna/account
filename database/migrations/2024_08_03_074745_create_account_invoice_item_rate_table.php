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
        Schema::create('account_invoice_item_rate', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')->constrained('account_invoice_item')->onDelete('cascade')->index('item_id');
            $table->foreignId('rate_id')->constrained('account_rate')->onDelete('cascade')->index('rate_id');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_invoice_item_rate');
    }
};
