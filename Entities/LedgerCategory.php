<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

class LedgerCategory extends BaseModel
{

    protected $fillable = ['name', 'slug', 'chart_id', 'parent_id', 'is_system'];
    public $migrationDependancy = ['account_chart_of_account'];
    protected $table = "account_ledger_category";

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $table->increments('id');
        $table->string('name')->nullable();
        $table->string('slug')->nullable();
        $table->foreignId('chart_id')->nullable();
        $table->foreignId('parent_id')->nullable();
        $table->tinyInteger('is_system')->nullable();
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_chart_of_account', 'chart_id');
        Migration::addForeign($table, 'account_ledger_category', 'parent_id');
    }
}
