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
     *
     * @var array<string>
     */
    protected $fillable = ['financial_year_id', 'chart_id', 'ledger_id', 'type', 'debit', 'credit'];



    /**
     * The fields that are to be render when performing relationship queries.
     *
     * @var array<string>
     */
    public $rec_names = ['financial_year_id', 'chart_id', 'ledger_id'];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = ['account_ledger', 'account_chart_of_account', 'account_financial_year'];

    /**
     * The fields that can be filled
     *
     * @var string
     */
    protected $table = "account_opening_balance";

    /**
     * Function for defining list of fields in table view.
     *
     * @return ListTable
     */
    public function listTable(): ListTable
    {
        // listing view fields
        $fields = new ListTable();

        $fields->name('financial_year_id')->type('recordpicker')->table(['account', 'financial_year'])->ordering(true);
        $fields->name('chart_id')->type('recordpicker')->table(['account', 'chart_of_account'])->ordering(true);
        $fields->name('ledger_id')->type('recordpicker')->table(['account', 'ledger'])->ordering(true);
        $fields->name('type')->type('text')->ordering(true);
        $fields->name('debit')->type('text')->ordering(true);
        $fields->name('credit')->type('text')->ordering(true);

        return $fields;

    }

    /**
     * Function for defining list of fields in form view.
     * 
     * @return FormBuilder
     */
    public function formBuilder(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('financial_year_id')->type('recordpicker')->table(['account', 'financial_year'])->group('w-1/2');
        $fields->name('chart_id')->type('recordpicker')->table(['account', 'chart_of_account'])->group('w-1/2');
        $fields->name('ledger_id')->type('recordpicker')->table(['account', 'ledger'])->group('w-1/2');
        $fields->name('type')->type('text')->group('w-1/2');
        $fields->name('debit')->type('text')->group('w-1/2');
        $fields->name('credit')->type('text')->group('w-1/2');

        return $fields;

    }

    /**
     * Function for defining list of fields in filter view.
     * 
     * @return FormBuilder
     */
    public function filter(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('financial_year_id')->type('recordpicker')->table(['account', 'financial_year'])->group('w-1/6');
        $fields->name('chart_id')->type('recordpicker')->table(['account', 'chart_of_account'])->group('w-1/6');
        $fields->name('ledger_id')->type('recordpicker')->table(['account', 'ledger'])->group('w-1/6');
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
    public function migration(Blueprint $table): void
    {
        $table->increments('id');
        $table->foreignId('financial_year_id')->nullable();
        $table->foreignId('chart_id')->nullable();
        $table->foreignId('ledger_id')->nullable();
        $table->string('type', 50)->nullable();
        $table->decimal('debit', 20, 2)->default(0.00);
        $table->decimal('credit', 20, 2)->default(0.00);
    }

    /**
     * Handle post migration processes for adding foreign keys.
     *
     * @param Blueprint $table
     *
     * @return void
     */
    public function post_migration(Blueprint $table): void
    {
        Migration::addForeign($table, 'account_financial_year', 'financial_year_id');
        Migration::addForeign($table, 'account_chart_of_account', 'chart_id');
        Migration::addForeign($table, 'account_ledger', 'ledger_id');
    }
}
