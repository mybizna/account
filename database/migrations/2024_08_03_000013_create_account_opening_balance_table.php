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

            $table->foreignId('financial_year_id')->nullable()->constrained('account_financial_year')->onDelete('set null');
            $table->foreignId('chart_id')->nullable()->constrained('account_chart_of_account')->onDelete('set null');
            $table->foreignId('ledger_id')->nullable()->constrained('account_ledger')->onDelete('set null');
            $table->string('type', 50)->nullable();
            $table->decimal('debit', 20, 2)->default(0.00);
            $table->decimal('credit', 20, 2)->default(0.00);

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
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
