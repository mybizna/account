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
        Schema::create('account_rate_file', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rate_id')->nullable()->constrained('account_rate')->onDelete('set null');
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->string('token')->nullable();
            $table->string('type')->nullable();
            $table->integer('max_limit')->nullable();
            $table->string('file')->nullable();
            $table->tinyInteger('is_processed')->nullable();

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
        Schema::dropIfExists('account_rate_file');
    }
};
