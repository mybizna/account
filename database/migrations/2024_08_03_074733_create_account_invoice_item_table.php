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
            $table->foreignId('invoice_id');
            $table->foreignId('ledger_id');
            $table->decimal('price', 20, 2)->default(0.00);
            $table->decimal('amount', 20, 2)->default(0.00);
            $table->string('module')->nullable();
            $table->string('model')->nullable();
            $table->foreignId('item_id')->nullable();
            $table->integer('quantity')->nullable();




            
            $table->timestamps();
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
