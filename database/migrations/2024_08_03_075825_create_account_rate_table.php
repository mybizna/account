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
        Schema::create('account_rate', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug');
            $table->foreignId('ledger_id')->constrained('account_ledger')->onDelete('cascade')->index('ledger_id');
            $table->decimal('value', 20, 2);
            $table->enum('method', ['+', '+%', '-', '-%'])->default('+');
            $table->string('params')->nullable();
            $table->tinyInteger('ordering')->nullable();
            $table->tinyInteger('on_total')->default(false);
            $table->tinyInteger('published')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_rate');
    }
};
