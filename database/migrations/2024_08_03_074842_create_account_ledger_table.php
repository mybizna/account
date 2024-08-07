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
        Schema::create('account_ledger', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chart_id')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->integer('code')->nullable();
            $table->tinyInteger('unused')->default(1);
            $table->tinyInteger('is_system')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_ledger');
    }
};
