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
        Schema::create('account_opening_balance', function (Blueprint $table) {
            $table->id();

            $table->foreignId('financial_year_id')->nullable();
            $table->foreignId('chart_id')->nullable();
            $table->foreignId('ledger_id')->nullable();
            $table->string('type', 50)->nullable();
            $table->decimal('debit', 20, 2)->default(0.00);
            $table->decimal('credit', 20, 2)->default(0.00);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_opening_balance');
    }
};
