<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

use Modules\Core\Classes\Views\ListTable;
use Modules\Core\Classes\Views\FormBuilder;

class Gateway extends BaseModel
{

    protected $fillable = [
        'title', 'slug', 'ledger_id', 'currency_id', 'image', 'url', 'instruction',
        'module', 'ordering', 'is_default', 'is_hidden', 'is_hide_in_invoice', 'published',

    ];
    public $migrationDependancy = ['core_currency', 'account_ledger'];
    protected $table = "account_gateway";

    protected $can_delete = "false";


    public function listTable(){
        // listing view fields
        $fields = new ListTable();

        $fields->name('title')->type('text')->ordering(true);
        $fields->name('slug')->type('text')->ordering(true);
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->ordering(true);
        $fields->name('currency_id')->type('recordpicker')->table('core_currency')->ordering(true);
        $fields->name('image')->type('text')->ordering(true);
        $fields->name('module')->type('text')->ordering(true);
        $fields->name('ordering')->type('text')->ordering(true);
        $fields->name('is_default')->type('text')->ordering(true);
        $fields->name('is_hidden')->type('text')->ordering(true);
        $fields->name('is_hide_in_invoice')->type('text')->ordering(true);
        $fields->name('published')->type('text')->ordering(true);

        return $fields;

    }
    
    public function formBuilder(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('title')->type('text')->group('w-1/2');
        $fields->name('slug')->type('text')->group('w-1/2');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/2');
        $fields->name('currency_id')->type('recordpicker')->table('core_currency')->group('w-1/2');
        $fields->name('image')->type('text')->group('w-1/2');
        $fields->name('url')->type('text')->group('w-1/2');
        $fields->name('module')->type('text')->group('w-1/2');
        $fields->name('instruction')->type('textarea')->group('w-full');
        $fields->name('ordering')->type('text')->group('w-1/2');
        $fields->name('is_default')->type('switch')->group('w-1/2');
        $fields->name('is_hidden')->type('switch')->group('w-1/2');
        $fields->name('is_hide_in_invoice')->type('switch')->group('w-1/2');
        $fields->name('published')->type('switch')->group('w-1/2');


        return $fields;

    }

    public function filter(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('title')->type('text')->group('w-1/2');
        $fields->name('slug')->type('text')->group('w-1/2');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/2');
        $fields->name('currency_id')->type('recordpicker')->table('core_currency')->group('w-1/2');
        $fields->name('image')->type('text')->group('w-1/2');
        $fields->name('url')->type('text')->group('w-1/2');
        $fields->name('module')->type('text')->group('w-1/2');
        $fields->name('instruction')->type('textarea')->group('w-full');
        $fields->name('ordering')->type('text')->group('w-1/2');
        $fields->name('is_default')->type('switch')->group('w-1/2');
        $fields->name('is_hidden')->type('switch')->group('w-1/2');
        $fields->name('is_hide_in_invoice')->type('switch')->group('w-1/2');
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
        $table->string('title');
        $table->string('slug');
        $table->foreignId('ledger_id');
        $table->foreignId('currency_id')->nullable();
        $table->string('image')->nullable();
        $table->string('url')->nullable();
        $table->string('module')->nullable();
        $table->string('instruction')->nullable();
        $table->integer('ordering')->nullable();
        $table->tinyInteger('is_default')->default(false);
        $table->tinyInteger('is_hidden')->default(false);
        $table->tinyInteger('is_hide_in_invoice')->default(true);
        $table->tinyInteger('published')->default(false);
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'core_currency', 'currency_id');
        Migration::addForeign($table, 'account_ledger', 'ledger_id');
    }
}
