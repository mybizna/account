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
        Schema::create('account_invoice_coupon', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')->constrained('account_invoice')->index('invoice_id');
            $table->foreignId('coupon_id')->constrained('account_coupon')->index('coupon_id');
    
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_invoice_coupon');
    }
};
