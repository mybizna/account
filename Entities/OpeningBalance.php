<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Views\FormBuilder;
use Modules\Base\Classes\Views\ListTable;
use Modules\Base\Entities\BaseModel;

class OpeningBalance extends BaseModel
{
    /**
     * The fields that can be filled
     * @var array<string>
     */
    protected $fillable = ['financial_year_id', 'chart_id', 'ledger_id', 'type', 'debit', 'credit'];

    /**
     * List of tables names that are need in this model during migration.
     * @var array<string>
     */
    public array $migrationDependancy = ['account_ledger', 'account_chart_of_account', 'account_financial_year'];

    /**
     * The fields that can be filled
     * @var array<string>
     */
    protected $table = "account_opening_balance";

    public function listTable(): ListTable
    {
        // listing view fields
        $fields = new ListTable();

        $fields->name('financial_year_id')->type('recordpicker')->table('account_financial_year')->ordering(true);
        $fields->name('chart_id')->type('recordpicker')->table('account_chart_of_account')->ordering(true);
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->ordering(true);
        $fields->name('type')->type('text')->ordering(true);
        $fields->name('debit')->type('text')->ordering(true);
        $fields->name('credit')->type('text')->ordering(true);

        return $fields;

    }

    public function formBuilder(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('financial_year_id')->type('recordpicker')->table('account_financial_year')->group('w-1/2');
        $fields->name('chart_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/2');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/2');
        $fields->name('type')->type('text')->group('w-1/2');
        $fields->name('debit')->type('text')->group('w-1/2');
        $fields->name('credit')->type('text')->group('w-1/2');

        return $fields;

    }

    public function filter(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('financial_year_id')->type('recordpicker')->table('account_financial_year')->group('w-1/6');
        $fields->name('chart_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/6');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/6');
        $fields->name('type')->type('text')->group('w-1/6');
        $fields->name('debit')->type('text')->group('w-1/6');
        $fields->name('credit')->type('text')->group('w-1/6');

        return $fields;

    }
    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
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
