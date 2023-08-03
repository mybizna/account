<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Views\FormBuilder;
use Modules\Base\Classes\Views\ListTable;
use Modules\Base\Entities\BaseModel;

class Journal extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['title', 'grouping_id', 'partner_id', 'ledger_id', 'payment_id', 'invoice_id', 'debit', 'credit', 'params'];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = ['partner', 'account_ledger'];

    /**
     * The fields that can be filled
     *
     * @var string
     */
    protected $table = "account_journal";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected $can_delete = false;

    /**
     * Function for defining list of fields in table view.
     *
     * @return ListTable
     */
    public function listTable(): ListTable
    {
        // listing view fields
        $fields = new ListTable();

        $fields->name('title')->type('text')->ordering(true);
        $fields->name('grouping_id')->type('text')->ordering(true);
        $fields->name('partner_id')->type('recordpicker')->table('partner')->ordering(true);
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->ordering(true);
        $fields->name('payment_id')->type('recordpicker')->table('account_payment')->ordering(true);
        $fields->name('invoice_id')->type('recordpicker')->table('account_invoice')->ordering(true);
        $fields->name('debit')->type('text')->ordering(true);
        $fields->name('credit')->type('text')->ordering(true);
        $fields->name('params')->type('text')->ordering(true);

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

        $fields->name('title')->type('text')->group('w-1/2');
        $fields->name('grouping_id')->type('text')->group('w-1/2');
        $fields->name('partner_id')->type('recordpicker')->table('partner')->group('w-1/2');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/2');
        $fields->name('payment_id')->type('recordpicker')->table('account_payment')->group('w-1/2');
        $fields->name('invoice_id')->type('recordpicker')->table('account_invoice')->group('w-1/2');
        $fields->name('debit')->type('text')->group('w-1/2');
        $fields->name('credit')->type('text')->group('w-1/2');
        $fields->name('params')->type('text')->group('w-1/2');

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

        $fields->name('title')->type('text')->group('w-1/6');
        $fields->name('grouping_id')->type('text')->group('w-1/6');
        $fields->name('partner_id')->type('recordpicker')->table('partner')->group('w-1/6');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/6');
        $fields->name('payment_id')->type('recordpicker')->table('account_payment')->group('w-1/6');
        $fields->name('invoice_id')->type('recordpicker')->table('account_invoice')->group('w-1/6');
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
        $table->string('title');
        $table->char('grouping_id');
        $table->foreignId('partner_id');
        $table->foreignId('ledger_id');
        $table->foreignId('payment_id')->nullable();
        $table->foreignId('invoice_id')->nullable();
        $table->decimal('debit', 20, 2)->nullable();
        $table->decimal('credit', 20, 2)->nullable();
        $table->string('params')->nullable();
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
        Migration::addForeign($table, 'partner', 'partner_id');
        Migration::addForeign($table, 'account_ledger', 'ledger_id');
        Migration::addForeign($table, 'account_payment', 'payment_id');
        Migration::addForeign($table, 'account_invoice', 'invoice_id');
    }
}
