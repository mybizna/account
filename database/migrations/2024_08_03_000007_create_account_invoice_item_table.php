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
        Schema::create('account_invoice_item', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->foreignId('invoice_id')->nullable()->constrained('account_invoice')->onDelete('set null');
            $table->foreignId('ledger_id')->nullable()->constrained('account_ledger')->onDelete('set null');
            $table->decimal('price', 20, 2)->default(0.00);
            $table->decimal('amount', 20, 2)->default(0.00);
            $table->string('module')->nullable();
            $table->string('model')->nullable();
            $table->bigInteger('item_id')->nullable();
            $table->integer('quantity')->nullable();

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
        Schema::dropIfExists('account_invoice_item');
    }
};
