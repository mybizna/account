<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Views\FormBuilder;
use Modules\Base\Classes\Views\ListTable;
use Modules\Base\Entities\BaseModel;

class ChartOfAccount extends BaseModel
{
    /**
     * The fields that can be filled
     * @var array<string>
     */
    protected $fillable = ['name', 'slug'];

    /**
     * List of tables names that are need in this model during migration.
     * @var array<string>
     */
    public array $migrationDependancy = [];

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "account_chart_of_account";

    /**
     * This model is not deletable.
     * @var string
     */
    protected string $can_delete = "false";

    public function listTable(): ListTable
    {
        // listing view fields
        $fields = new ListTable();

        $fields->name('name')->type('text')->group('w-1/2');
        $fields->name('slug')->type('text')->group('w-1/2');

        return $fields;

    }

    public function formBuilder(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('name')->type('text')->group('w-1/2');
        $fields->name('slug')->type('text')->group('w-1/2');

        return $fields;

    }

    public function filter(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('name')->type('text')->group('w-1/2');
        $fields->name('slug')->type('text')->group('w-1/2');

        return $fields;

    }

    /**
     * Migration for creating table.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {

        $table->increments('id');
        $table->string('name')->nullable();
        $table->string('slug')->nullable();
    }
}
