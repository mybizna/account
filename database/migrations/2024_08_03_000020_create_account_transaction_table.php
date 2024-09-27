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
            $table->foreignId('partner_id')->nullable()->constrained('partner_partner')->onDelete('set null');
            $table->foreignId('left_chart_of_account_id')->nullable()->constrained('account_chart_of_account')->onDelete('set null');
            $table->foreignId('left_ledger_id')->nullable()->constrained('account_ledger')->onDelete('set null');
            $table->foreignId('right_chart_of_account_id')->nullable()->constrained('account_chart_of_account')->onDelete('set null');
            $table->foreignId('right_ledger_id')->nullable()->constrained('account_ledger')->onDelete('set null');
            $table->tinyInteger('is_processed')->nullable();

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
        Schema::dropIfExists('account_transaction');
    }
};
