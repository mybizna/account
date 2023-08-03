<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Views\FormBuilder;
use Modules\Base\Classes\Views\ListTable;
use Modules\Base\Entities\BaseModel;

class Transaction extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'amount', 'description', 'partner_id', 'ledger_setting_id', 'left_chart_of_account_id', 'left_ledger_id',
        'right_chart_of_account_id', 'right_ledger_id', 'is_processed',
    ];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = ['partner', 'account_payment', 'account_rate'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_transaction";

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

        $fields->name('amount')->type('text')->ordering(true);
        $fields->name('partner_id')->type('recordpicker')->table('partner')->ordering(true);
        $fields->name('ledger_setting_id')->type('recordpicker')->table('account_ledger_setting')->ordering(true);
        $fields->name('left_chart_of_account_id')->type('recordpicker')->table('account_chart_of_account')->ordering(true);
        $fields->name('left_ledger_id')->type('recordpicker')->table('account_ledger')->ordering(true);
        $fields->name('right_chart_of_account_id')->type('recordpicker')->table('account_chart_of_account')->ordering(true);
        $fields->name('right_ledger_id')->type('recordpicker')->table('account_ledger')->ordering(true);
        $fields->name('is_processed')->type('switch')->ordering(true);

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

        $fields->name('amount')->type('text')->group('w-1/2');
        $fields->name('partner_id')->type('recordpicker')->table('partner')->group('w-1/2');
        $fields->name('ledger_setting_id')->type('recordpicker')->table('account_ledger_setting')->group('w-1/2');
        $fields->name('left_chart_of_account_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/2');
        $fields->name('left_ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/2');
        $fields->name('right_chart_of_account_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/2');
        $fields->name('right_ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/2');
        $fields->name('is_processed')->type('switch')->group('w-1/2');
        $fields->name('description')->type('text')->group('w-full');

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

        $fields->name('amount')->type('text')->group('w-1/6');
        $fields->name('partner_id')->type('recordpicker')->table('partner')->group('w-1/6');
        $fields->name('ledger_setting_id')->type('recordpicker')->table('account_ledger_setting')->group('w-1/6');
        $fields->name('left_chart_of_account_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/6');
        $fields->name('left_ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/6');
        $fields->name('right_chart_of_account_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/6');
        $fields->name('right_ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/6');
        $fields->name('is_processed')->type('switch')->group('w-1/6');

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
        $table->decimal('amount', 20, 2)->default(0.00);
        $table->string('description');
        $table->foreignId('partner_id');
        $table->foreignId('ledger_setting_id')->nullable();
        $table->foreignId('left_chart_of_account_id')->nullable();
        $table->foreignId('left_ledger_id')->nullable();
        $table->foreignId('right_chart_of_account_id')->nullable();
        $table->foreignId('right_ledger_id')->nullable();
        $table->tinyInteger('is_processed')->nullable();
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
        Migration::addForeign($table, 'account_ledger_setting', 'ledger_setting_id');
        Migration::addForeign($table, 'partner', 'partner_id');
        Migration::addForeign($table, 'account_ledger', 'left_ledger_id');
        Migration::addForeign($table, 'account_chart_of_account', 'right_chart_of_account_id');
        Migration::addForeign($table, 'account_ledger', 'right_ledger_id');
    }
}
