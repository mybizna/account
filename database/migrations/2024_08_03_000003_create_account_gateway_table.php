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
            $table->foreignId('ledger_id')->nullable()->constrained('account_ledger')->onDelete('set null');
            $table->foreignId('currency_id')->nullable()->constrained('core_currency')->onDelete('set null');
            $table->string('image')->nullable();
            $table->string('url')->nullable();
            $table->string('module')->nullable();
            $table->string('instruction')->nullable();
            $table->integer('ordering')->nullable();
            $table->tinyInteger('is_default')->default(0);
            $table->tinyInteger('is_hidden')->default(0);
            $table->tinyInteger('is_hide_in_invoice')->default(1);
            $table->tinyInteger('published')->default(0);

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
        Schema::dropIfExists('account_gateway');
    }
};
