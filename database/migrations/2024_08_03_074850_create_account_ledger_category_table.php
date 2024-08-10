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
        Schema::create('account_ledger_category', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug');
            $table->foreignId('chart_id')->constrained('account_chart')->onDelete('cascade')->index('chart_id');
            $table->foreignId('parent_id')->constrained('account_ledger_category')->onDelete('cascade')->index('parent_id');
            $table->tinyInteger('is_system');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_ledger_category');
    }
};
