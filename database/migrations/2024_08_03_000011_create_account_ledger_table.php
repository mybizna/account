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

            $table->foreignId('chart_id')->constrained('account_chart_of_account')->onDelete('cascade')->nullable()->index('account_ledger_chart_id');
            $table->foreignId('category_id')->constrained('account_category')->onDelete('cascade')->nullable()->index('account_ledger_category_id');
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
