<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

class ChartOfAccount extends Model
{

    protected $fillable = [];
    protected $migrationOrder = 5;
    protected $table = "account_chart_of_account";

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {

        $table->integer('id')->primary();
        $table->string('name')->nullable();
        $table->string('slug')->nullable();
        $table->string('created_by', 50)->nullable();
        $table->string('updated_by', 50)->nullable();
        $table->timestamps();
    }
}
