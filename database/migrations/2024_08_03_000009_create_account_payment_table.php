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
            $table->foreignId('ledger_id')->nullable()->constrained('account_ledger')->onDelete('set null');
            $table->foreignId('partner_id')->nullable()->constrained('partner_partner')->onDelete('set null');
            $table->foreignId('gateway_id')->nullable()->constrained('account_gateway')->onDelete('set null');
            $table->string('receipt_no')->nullable();
            $table->string('code')->nullable();
            $table->string('others')->nullable();
            $table->enum('stage', ['pending', 'wallet', 'posted'])->default('pending');
            $table->enum('status', ['pending', 'paid', 'reversed', 'canceled'])->default('pending');
            $table->enum('type', ['in', 'out'])->default('in');
            $table->tinyInteger('is_posted')->default(false);

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
        Schema::dropIfExists('account_payment');
    }
};
