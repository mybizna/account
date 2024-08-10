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

            $table->foreignId('financial_year_id')->constrained('account_financial_year')->onDelete('cascade')->nullable()->index('financial_year_id');
            $table->foreignId('chart_id')->constrained('account_chart')->onDelete('cascade')->nullable()->index('chart_id');
            $table->foreignId('ledger_id')->constrained('account_ledger')->onDelete('cascade')->nullable()->index('ledger_id');
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
