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
        Schema::create('account_gateway', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug');
            $table->foreignId('ledger_id');
            $table->foreignId('currency_id')->nullable();
            $table->string('image')->nullable();
            $table->string('url')->nullable();
            $table->string('module')->nullable();
            $table->string('instruction')->nullable();
            $table->integer('ordering')->nullable();
            $table->tinyInteger('is_default')->default(0);
            $table->tinyInteger('is_hidden')->default(0);
            $table->tinyInteger('is_hide_in_invoice')->default(1);
            $table->tinyInteger('published')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_gateway');
    }
};
