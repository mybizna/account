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
            $table->foreignId('partner_id');
            $table->foreignId('ledger_id');
            $table->foreignId('payment_id')->nullable();
            $table->foreignId('invoice_id')->nullable();
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
