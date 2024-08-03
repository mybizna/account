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
        Schema::create('account_payment', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->decimal('amount', 20, 2);
            $table->foreignId('ledger_id');
            $table->foreignId('partner_id');
            $table->foreignId('gateway_id');
            $table->string('receipt_no')->nullable();
            $table->string('code')->nullable();
            $table->string('others')->nullable();
            $table->enum('stage', ['pending', 'wallet', 'posted'])->default('pending');
            $table->enum('status', ['pending', 'paid', 'reversed', 'canceled'])->default('pending');
            $table->enum('type', ['in', 'out'])->default('in');
            $table->tinyInteger('is_posted')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_payment');
    }
};
