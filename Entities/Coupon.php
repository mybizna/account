<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Views\FormBuilder;
use Modules\Base\Classes\Views\ListTable;
use Modules\Base\Entities\BaseModel;

class Coupon extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'code', 'description', 'value', 'start_amount', 'end_amount',
        'start_date', 'end_date', 'applied', 'is_percent', 'published', 'is_visible',
    ];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_coupon";

    /**
     * Function for defining list of fields in table view.
     *
     * @return ListTable
     */
    public function listTable(): ListTable
    {
        // listing view fields
        $fields = new ListTable();
        $fields->name('code')->type('text')->ordering(true);
        $fields->name('description')->type('text')->ordering(true);
        $fields->name('value')->type('text')->ordering(true);
        $fields->name('start_amount')->type('text')->ordering(true);
        $fields->name('end_amount')->type('text')->ordering(true);
        $fields->name('start_date')->type('text')->ordering(true);
        $fields->name('end_date')->type('text')->ordering(true);
        $fields->name('applied')->type('text')->ordering(true);
        $fields->name('is_percent')->type('text')->ordering(true);
        $fields->name('published')->type('text')->ordering(true);

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

        $fields->name('code')->type('text')->group('w-1/2');
        $fields->name('description')->type('textarea')->group('w-full');
        $fields->name('value')->type('text')->group('w-1/2');
        $fields->name('start_amount')->type('text')->group('w-1/2');
        $fields->name('end_amount')->type('text')->group('w-1/2');
        $fields->name('start_date')->type('date')->group('w-1/2');
        $fields->name('end_date')->type('date')->group('w-1/2');
        $fields->name('applied')->type('switch')->group('w-1/2');
        $fields->name('is_percent')->type('switch')->group('w-1/2');
        $fields->name('published')->type('switch')->group('w-1/2');

        return $fields;

    }

    /**
     * Function for defining list of fields used for filtering.
     * 
     * @return FormBuilder
     */
    public function filter(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('code')->type('text')->group('w-1/2');
        $fields->name('applied')->type('switch')->group('w-1/2');
        $fields->name('is_percent')->type('switch')->group('w-1/2');
        $fields->name('published')->type('switch')->group('w-1/2');

        return $fields;

    }
    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     *
     * @return void
     */
    public function migration(Blueprint $table): void
    {
        $table->increments('id');
        $table->string('code')->nullable();
        $table->string('description')->nullable();
        $table->string('value')->nullable();
        $table->decimal('start_amount', 20, 2)->default(0.00);
        $table->decimal('end_amount', 20, 2)->default(0.00);
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->string('applied')->nullable();
        $table->tinyInteger('is_percent')->default(0);
        $table->tinyInteger('published')->default(0);
        $table->tinyInteger('is_visible')->default(false);
    }
}
