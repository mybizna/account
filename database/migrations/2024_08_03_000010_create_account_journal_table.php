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
        Schema::create('account_journal', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->char('grouping_id');
            $table->foreignId('partner_id')->constrained('partner_partner')->onDelete('cascade')->index('account_journal_partner_id');
            $table->foreignId('ledger_id')->constrained('account_ledger')->onDelete('cascade')->index('account_journal_ledger_id');
            $table->foreignId('payment_id')->constrained('account_payment')->onDelete('cascade')->nullable()->index('account_journal_payment_id');
            $table->foreignId('invoice_id')->constrained('account_invoice')->onDelete('cascade')->nullable()->index('account_journal_invoice_id');
            $table->decimal('debit', 20, 2)->nullable();
            $table->decimal('credit', 20, 2)->nullable();
            $table->string('params')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_journal');
    }
};
