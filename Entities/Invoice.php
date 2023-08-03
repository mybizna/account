<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Views\FormBuilder;
use Modules\Base\Classes\Views\ListTable;
use Modules\Base\Entities\BaseModel;

class Invoice extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'invoice_no', 'partner_id', 'due_date', 'module', 'model', 'status',
        'description', 'is_posted', 'total',
    ];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = ['partner'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice";

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
        $fields->name('invoice_no')->type('text')->ordering(true);
        $fields->name('partner_id')->type('recordpicker')->table('partner')->ordering(true);
        $fields->name('due_date')->type('datetime')->ordering(true);
        $fields->name('module')->type('text')->ordering(true);
        $fields->name('model')->type('text')->ordering(true);
        $fields->name('status')->type('switch')->ordering(true);
        $fields->name('description')->type('textarea')->ordering(true);
        $fields->name('is_posted')->type('switch')->ordering(true);

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
        $fields->name('invoice_no')->type('text')->group('w-1/2');
        $fields->name('partner_id')->type('recordpicker')->table('partner')->group('w-1/2');
        $fields->name('due_date')->type('datetime')->group('w-1/2');
        $fields->name('module')->type('text')->group('w-1/2');
        $fields->name('model')->type('text')->group('w-1/2');
        $fields->name('status')->type('switch')->group('w-1/2');
        $fields->name('description')->type('textarea')->group('w-full');
        $fields->name('is_posted')->type('switch')->group('w-1/2');

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
        $fields->name('invoice_no')->type('text')->group('w-1/6');
        $fields->name('partner_id')->type('recordpicker')->table('partner')->group('w-1/6');
        $fields->name('due_date')->type('datetime')->group('w-1/6');
        $fields->name('module')->type('text')->group('w-1/6');
        $fields->name('model')->type('text')->group('w-1/6');
        $fields->name('status')->type('switch')->group('w-1/6');
        $fields->name('is_posted')->type('switch')->group('w-1/6');

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
        $table->char('invoice_no', 100);
        $table->foreignId('partner_id');
        $table->date('due_date')->useCurrent();
        $table->string('module')->default('Account');
        $table->string('model')->default('Invoice');
        $table->enum('status', ['draft', 'pending', 'partial', 'paid', 'closed', 'void'])->default('draft')->nullable();
        $table->string('description')->nullable();
        $table->tinyInteger('is_posted')->default(false);
        $table->decimal('total', 20, 2)->nullable();
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
    }
}
