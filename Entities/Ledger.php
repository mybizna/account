<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Views\FormBuilder;
use Modules\Base\Classes\Views\ListTable;
use Modules\Base\Entities\BaseModel;

class Ledger extends BaseModel
{

    /**
     * The fields that can be filled
     * @var array<string>
     */
    protected $fillable = ['chart_id', 'category_id', 'name', 'slug', 'code', 'unused', 'is_system'];

    /**
     * List of tables names that are need in this model during migration.
     * @var array<string>
     */
    public array $migrationDependancy = ['account_chart_of_account', 'account_ledger_category'];

    /**
     * The fields that can be filled
     * @var array<string>
     */
    protected $table = "account_ledger";

    protected $can_delete = "false";

    public function listTable(): ListTable
    {
        // listing view fields
        $fields = new ListTable();

        $fields->name('chart_id')->type('recordpicker')->table('account_chart_of_account')->ordering(true);
        $fields->name('category_id')->type('recordpicker')->table('account_ledger_category')->ordering(true);
        $fields->name('name')->type('text')->ordering(true);
        $fields->name('slug')->type('text')->ordering(true);
        $fields->name('code')->type('text')->ordering(true);
        $fields->name('unused')->type('switch')->ordering(true);
        $fields->name('is_system')->type('switch')->ordering(true);

        return $fields;

    }

    public function formBuilder(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('chart_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/2');
        $fields->name('category_id')->type('recordpicker')->table('account_ledger_category')->group('w-1/2');
        $fields->name('name')->type('text')->group('w-1/2');
        $fields->name('slug')->type('text')->group('w-1/2');
        $fields->name('code')->type('text')->group('w-1/2');
        $fields->name('unused')->type('switch')->group('w-1/2');
        $fields->name('is_system')->type('switch')->group('w-1/2');

        return $fields;

    }

    public function filter(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('chart_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/6');
        $fields->name('category_id')->type('recordpicker')->table('account_ledger_category')->group('w-1/6');
        $fields->name('name')->type('text')->group('w-1/6');
        $fields->name('code')->type('text')->group('w-1/6');
        $fields->name('unused')->type('switch')->group('w-1/6');
        $fields->name('is_system')->type('switch')->group('w-1/6');

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
        $table->foreignId('chart_id')->nullable();
        $table->foreignId('category_id')->nullable();
        $table->string('name')->nullable();
        $table->string('slug')->nullable();
        $table->integer('code')->nullable();
        $table->tinyInteger('unused')->default(1);
        $table->tinyInteger('is_system')->default(0);
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_chart_of_account', 'chart_id');
        Migration::addForeign($table, 'account_ledger_category', 'category_id');
    }

    public function deleteRecord($id)
    {

        $ledger = $this->where('id', $id)->first();

        if ($ledger->is_system) {
            return [
                'module' => $this->module,
                'model' => $this->model,
                'status' => 0,
                'error' => 1,
                'record' => [],
                'message' => 'You can not Delete a Lerger Set by system.',
            ];
        }

        parent::deleteRecord($id);

    }
}
