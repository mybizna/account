<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

use Modules\Core\Classes\Views\ListTable;
use Modules\Core\Classes\Views\FormBuilder;

class LedgerCategory extends BaseModel
{

    protected $fillable = ['name', 'slug', 'chart_id', 'parent_id', 'is_system'];
    public $migrationDependancy = ['account_chart_of_account'];
    protected $table = "account_ledger_category";


    public function listTable(){
        // listing view fields
        $fields = new ListTable();

        $fields->name('name')->type('text')->ordering(true);
        $fields->name('slug')->type('text')->ordering(true);
        $fields->name('chart_id')->type('recordpicker')->table('account_chart_of_account')->ordering(true);
        $fields->name('parent_id')->type('recordpicker')->table('account_ledger_category')->ordering(true);
        $fields->name('is_system')->type('switch')->ordering(true);


        return $fields;

    }
    
    public function formBuilder(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('name')->type('text')->group('w-1/2');
        $fields->name('slug')->type('text')->group('w-1/2');
        $fields->name('chart_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/2');
        $fields->name('parent_id')->type('recordpicker')->table('account_ledger_category')->group('w-1/2');
        $fields->name('is_system')->type('switch')->group('w-1/2');

        return $fields;

    }

    public function filter(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('name')->type('text')->group('w-1/6');
        $fields->name('chart_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/6');
        $fields->name('parent_id')->type('recordpicker')->table('account_ledger_category')->group('w-1/6');
        $fields->name('is_system')->type('switch')->group('w-1/6');

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
        $table->string('name')->nullable();
        $table->string('slug')->nullable();
        $table->foreignId('chart_id')->nullable();
        $table->foreignId('parent_id')->nullable();
        $table->tinyInteger('is_system')->nullable();
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_chart_of_account', 'chart_id');
        Migration::addForeign($table, 'account_ledger_category', 'parent_id');
    }
}
