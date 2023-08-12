<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Views\FormBuilder;
use Modules\Base\Classes\Views\ListTable;
use Modules\Base\Entities\BaseModel;

class RateFile extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'rate_id', 'year', 'month', 'token', 'type', 'max_limit', 'file', 'is_processed',
    ];
    /**
     * The fields that are to be render when performing relationship queries.
     *
     * @var array<string>
     */
    public $rec_names = ['rate_id', 'year', 'month'];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = ['account_rate'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_rate_file";

/**
 * Function for defining list of fields in table view.
 *
 * @var string
 *
 * @return ListTable
 */
    public function listTable(): ListTable
    {
        // listing view fields
        $fields = new ListTable();

        $fields->name('rate_id')->type('recordpicker')->table(['account', 'rate'])->ordering(true);
        $fields->name('year')->type('text')->ordering(true);
        $fields->name('month')->type('text')->ordering(true);
        $fields->name('token')->type('text')->ordering(true);
        $fields->name('type')->type('text')->ordering(true);
        $fields->name('max_limit')->type('text')->ordering(true);
        $fields->name('file')->type('text')->ordering(true);
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

        $fields->name('rate_id')->type('recordpicker')->table(['account', 'rate'])->group('w-1/2');
        $fields->name('year')->type('text')->group('w-1/2');
        $fields->name('month')->type('text')->group('w-1/2');
        $fields->name('token')->type('text')->group('w-1/2');
        $fields->name('type')->type('text')->group('w-1/2');
        $fields->name('max_limit')->type('text')->group('w-1/2');
        $fields->name('file')->type('text')->group('w-1/2');
        $fields->name('is_processed')->type('switch')->group('w-1/2');

        return $fields;

    }
/**
 * Function for defining list of fields in filter view.
 *
 * @var string
 *
 * @return FormBuilder
 */
    public function filter(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('rate_id')->type('recordpicker')->table(['account', 'rate'])->group('w-1/6');
        $fields->name('year')->type('text')->group('w-1/6');
        $fields->name('month')->type('text')->group('w-1/6');
        $fields->name('type')->type('text')->group('w-1/6');

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
        $table->foreignId('rate_id');
        $table->string('year');
        $table->string('month');
        $table->string('token');
        $table->string('type');
        $table->integer('max_limit');
        $table->string('file');
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
        Migration::addForeign($table, 'account_rate', 'rate_id');
    }
}
