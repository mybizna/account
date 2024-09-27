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
        Schema::create('account_coupon', function (Blueprint $table) {
            $table->id();

            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->string('value')->nullable();
            $table->decimal('start_amount', 20, 2)->default(0.00);
            $table->decimal('end_amount', 20, 2)->default(0.00);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('applied')->nullable();
            $table->tinyInteger('is_percent')->default(0);
            $table->tinyInteger('published')->default(0);
            $table->tinyInteger('is_visible')->default(0);

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
        Schema::dropIfExists('account_coupon');
    }
};
