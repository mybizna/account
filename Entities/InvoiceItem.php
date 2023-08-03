<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Views\FormBuilder;
use Modules\Base\Classes\Views\ListTable;
use Modules\Base\Entities\BaseModel;

class InvoiceItem extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'invoice_id', 'ledger_id', 'price', 'amount', 'quantity',
        'module', 'model', 'item_id',
    ];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = ['account_invoice', 'account_transaction'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice_item";

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
        $fields->name('invoice_id')->type('recordpicker')->table('account_invoice')->ordering(true);
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->ordering(true);
        $fields->name('price')->type('text')->ordering(true);
        $fields->name('amount')->type('text')->ordering(true);
        $fields->name('module')->type('text')->ordering(true);
        $fields->name('model')->type('text')->ordering(true);
        $fields->name('item_id')->type('text')->ordering(true);
        $fields->name('quantity')->type('text')->ordering(true);

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
        $fields->name('invoice_id')->type('recordpicker')->table('account_invoice')->group('w-1/2');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/2');
        $fields->name('price')->type('text')->group('w-1/2');
        $fields->name('amount')->type('text')->group('w-1/2');
        $fields->name('module')->type('text')->group('w-1/2');
        $fields->name('model')->type('text')->group('w-1/2');
        $fields->name('item_id')->type('text')->group('w-1/2');
        $fields->name('quantity')->type('text')->group('w-1/2');

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
        $fields->name('invoice_id')->type('recordpicker')->table('account_invoice')->group('w-1/6');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/6');
        $fields->name('price')->type('text')->group('w-1/6');
        $fields->name('amount')->type('text')->group('w-1/6');
        $fields->name('module')->type('text')->group('w-1/6');
        $fields->name('model')->type('text')->group('w-1/6');
        $fields->name('quantity')->type('text')->group('w-1/6');

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
        $table->foreignId('invoice_id');
        $table->foreignId('ledger_id');
        $table->decimal('price', 20, 2)->default(0.00);
        $table->decimal('amount', 20, 2)->default(0.00);
        $table->string('module')->nullable();
        $table->string('model')->nullable();
        $table->foreignId('item_id')->nullable();
        $table->integer('quantity')->nullable();
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
        Migration::addForeign($table, 'account_invoice', 'invoice_id');
        Migration::addForeign($table, 'account_transaction', 'transaction_id');
        Migration::addForeign($table, 'account_ledger', 'ledger_id');
    }
}
