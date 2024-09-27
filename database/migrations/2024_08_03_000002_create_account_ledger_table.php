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

            $table->foreignId('chart_id')->nullable()->constrained('account_chart_of_account')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('account_ledger_category')->onDelete('set null');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->integer('code')->nullable();
            $table->tinyInteger('unused')->default(1);
            $table->tinyInteger('is_system')->default(0);

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
        Schema::dropIfExists('account_ledger');
    }
};
