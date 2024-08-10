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
        Schema::create('account_transaction', function (Blueprint $table) {
            $table->id();

            $table->decimal('amount', 20, 2)->default(0.00);
            $table->string('description');
            $table->foreignId('partner_id')->constrained('partner_partner')->onDelete('cascade')->index('account_transaction_partner_id');
            $table->foreignId('left_chart_of_account_id')->constrained('account_chart_of_account')->onDelete('cascade')->nullable()->index('account_transaction_left_chart_of_account_id');
            $table->foreignId('left_ledger_id')->constrained('account_ledger')->onDelete('cascade')->nullable()->index('account_transaction_left_ledger_id');
            $table->foreignId('right_chart_of_account_id')->constrained('account_chart_of_account')->onDelete('cascade')->nullable()->index('account_transaction_right_chart_of_account_id');
            $table->foreignId('right_ledger_id')->constrained('account_ledger')->onDelete('cascade')->nullable()->index('account_transaction_right_ledger_id');
            $table->tinyInteger('is_processed')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_transaction');
    }
};
