<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Views\FormBuilder;
use Modules\Base\Classes\Views\ListTable;
use Modules\Base\Entities\BaseModel;

class InvoiceItemRate extends BaseModel
{
    /**
     * The fields that can be filled
     * @var array<string>
     */
    protected $fillable = [
        'title', 'slug', 'rate_id', 'invoice_item_id', 'method', 'value', 'params', 'ordering', 'on_total',
    ];
    /**
     * List of tables names that are need in this model during migration.
     * @var array<string>
     */
    public array $migrationDependancy = ['account_invoice_item', 'account_rate'];
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "account_invoice_item_rate";

    public function listTable(): ListTable
    {
        // listing view fields
        $fields = new ListTable();

        $fields->name('title')->type('text')->ordering(true);
        $fields->name('slug')->type('text')->ordering(true);
        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->ordering(true);
        $fields->name('invoice_item_id')->type('recordpicker')->table('account_invoice_item')->ordering(true);
        $fields->name('method')->type('text')->ordering(true);
        $fields->name('value')->type('text')->ordering(true);
        $fields->name('params')->type('text')->ordering(true);
        $fields->name('ordering')->type('text')->ordering(true);
        $fields->name('on_total')->type('text')->ordering(true);

        return $fields;

    }

    public function formBuilder(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('title')->type('text')->group('w-1/2');
        $fields->name('slug')->type('text')->group('w-1/2');
        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->group('w-1/2');
        $fields->name('invoice_item_id')->type('recordpicker')->table('account_invoice_item')->group('w-1/2');
        $fields->name('method')->type('text')->group('w-1/2');
        $fields->name('value')->type('text')->group('w-1/2');
        $fields->name('params')->type('text')->group('w-1/2');
        $fields->name('ordering')->type('text')->group('w-1/2');
        $fields->name('on_total')->type('text')->group('w-1/2');

        return $fields;

    }

    public function filter(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('title')->type('text')->group('w-1/6');
        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->group('w-1/6');
        $fields->name('invoice_item_id')->type('recordpicker')->table('account_invoice_item')->group('w-1/6');
        $fields->name('method')->type('text')->group('w-1/6');
        $fields->name('value')->type('text')->group('w-1/6');

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
        $table->integer('title');
        $table->integer('slug');
        $table->foreignId('rate_id');
        $table->foreignId('invoice_item_id');
        $table->enum('method', ['+', '+%', '-', '-%'])->default('+');
        $table->decimal('value', 20, 2)->default(0.00);
        $table->string('params')->nullable();
        $table->tinyInteger('ordering')->nullable();
        $table->tinyInteger('on_total')->default(false);
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_invoice', 'invoice_id');
        Migration::addForeign($table, 'account_transaction', 'rate_id');
    }
}
