<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

use Modules\Core\Classes\Views\ListTable;
use Modules\Core\Classes\Views\FormBuilder;

class RateFile extends BaseModel
{

    protected $fillable = [
        'rate_id', 'year', 'month', 'token', 'type', 'max_limit', 'file', 'is_processed'
    ];
    public $migrationDependancy = ['account_rate'];
    protected $table = "account_rate_file";


    public function listTable(){
        // listing view fields
        $fields = new ListTable();

        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->ordering(true);
        $fields->name('year')->type('text')->ordering(true);
        $fields->name('month')->type('text')->ordering(true);
        $fields->name('token')->type('text')->ordering(true);
        $fields->name('type')->type('text')->ordering(true);
        $fields->name('max_limit')->type('text')->ordering(true);
        $fields->name('file')->type('text')->ordering(true);
        $fields->name('is_processed')->type('switch')->ordering(true);

        return $fields;

    }
    
    public function formBuilder(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->group('w-1/2');
        $fields->name('year')->type('text')->group('w-1/2');
        $fields->name('month')->type('text')->group('w-1/2');
        $fields->name('token')->type('text')->group('w-1/2');
        $fields->name('type')->type('text')->group('w-1/2');
        $fields->name('max_limit')->type('text')->group('w-1/2');
        $fields->name('file')->type('text')->group('w-1/2');
        $fields->name('is_processed')->type('switch')->group('w-1/2');


        return $fields;

    }

    public function filter(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->group('w-1/6');
        $fields->name('year')->type('text')->group('w-1/6');
        $fields->name('month')->type('text')->group('w-1/6');
        $fields->name('type')->type('text')->group('w-1/6');

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
        $table->foreignId('rate_id');
        $table->string('year');
        $table->string('month');
        $table->string('token');
        $table->string('type');
        $table->integer('max_limit');
        $table->string('file');
        $table->tinyInteger('is_processed')->nullable();
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_rate', 'rate_id');
    }
}
