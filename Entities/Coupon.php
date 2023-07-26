<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Entities\BaseModel;
use Modules\Core\Classes\Views\FormBuilder;
use Modules\Core\Classes\Views\ListTable;

class Coupon extends BaseModel
{

    protected $fillable = [
        'code', 'description', 'value', 'start_amount', 'end_amount',
        'start_date', 'end_date', 'applied', 'is_percent', 'published', 'is_visible',
    ];
    public $migrationDependancy = [];
    protected $table = "account_coupon";

    public function listTable()
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

    public function formBuilder()
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

    public function filter()
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
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
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
