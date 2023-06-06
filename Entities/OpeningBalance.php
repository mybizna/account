<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

class OpeningBalance extends BaseModel
{

    protected $fillable = ['financial_year_id', 'chart_id', 'ledger_id', 'type', 'debit', 'credit'];
    public $migrationDependancy = ['account_ledger', 'account_chart_of_account', 'account_financial_year'];
    protected $table = "account_opening_balance";

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $table->increments('id');
        $table->foreignId('financial_year_id')->nullable();
        $table->foreignId('chart_id')->nullable();
        $table->foreignId('ledger_id')->nullable();
        $table->string('type', 50)->nullable();
        $table->decimal('debit', 20, 2)->default(0.00);
        $table->decimal('credit', 20, 2)->default(0.00);
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_financial_year', 'financial_year_id');
        Migration::addForeign($table, 'account_chart_of_account', 'chart_id');
        Migration::addForeign($table, 'account_ledger', 'ledger_id');
    }
}
