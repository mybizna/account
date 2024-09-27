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
        Schema::create('account_invoice_item_rate', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug');
            $table->foreignId('rate_id')->nullable()->constrained('account_rate')->onDelete('set null');
            $table->foreignId('invoice_item_id')->nullable()->constrained('account_invoice_item')->onDelete('set null');
            $table->enum('method', ['+', '+%', '-', '-%'])->default('+');
            $table->decimal('value', 20, 2)->default(0.00);
            $table->string('params')->nullable();
            $table->tinyInteger('ordering')->nullable();
            $table->tinyInteger('on_total')->default(false);

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
        Schema::dropIfExists('account_invoice_item_rate');
    }
};
