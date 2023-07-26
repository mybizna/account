<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

use Modules\Core\Classes\Views\ListTable;
use Modules\Core\Classes\Views\FormBuilder;

class InvoiceItemRate extends BaseModel
{

    protected $fillable = [
        'title', 'slug', 'rate_id', 'invoice_item_id', 'method', 'value', 'params', 'ordering', 'on_total',
    ];
    public $migrationDependancy = ['account_invoice_item', 'account_rate'];
    protected $table = "account_invoice_item_rate";


    public function listTable(){
        // listing view fields
        $fields = new ListTable();

        $fields->name('title')->type('text')->ordering(true);
        $fields->name('slug')->type('text')->ordering(true);
        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->ordering(true);
        $fields->name('invoice_item_id')->type('recordpicker')->table('account_invoice_item')->ordering(true);
        $fields->name('method')->type('text')->ordering(true);
        $fields->name('value')->type('text')->ordering(true);
        $fields->name('params')->type('text')->ordering(true);
        $fields->name('ordering')->type('text')->ordering(true);
        $fields->name('on_total')->type('text')->ordering(true);


        return $fields;

    }
    
    public function formBuilder(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('title')->type('text')->group('w-1/2');
        $fields->name('slug')->type('text')->group('w-1/2');
        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->group('w-1/2');
        $fields->name('invoice_item_id')->type('recordpicker')->table('account_invoice_item')->group('w-1/2');
        $fields->name('method')->type('text')->group('w-1/2');
        $fields->name('value')->type('text')->group('w-1/2');
        $fields->name('params')->type('text')->group('w-1/2');
        $fields->name('ordering')->type('text')->group('w-1/2');
        $fields->name('on_total')->type('text')->group('w-1/2');


        return $fields;

    }

    public function filter(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('title')->type('text')->group('w-1/6');
        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->group('w-1/6');
        $fields->name('invoice_item_id')->type('recordpicker')->table('account_invoice_item')->group('w-1/6');
        $fields->name('method')->type('text')->group('w-1/6');
        $fields->name('value')->type('text')->group('w-1/6');


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
        $table->integer('title');
        $table->integer('slug');
        $table->foreignId('rate_id');
        $table->foreignId('invoice_item_id');
        $table->enum('method', ['+', '+%', '-', '-%'])->default('+');
        $table->decimal('value', 20, 2)->default(0.00);
        $table->string('params')->nullable();
        $table->tinyInteger('ordering')->nullable();
        $table->tinyInteger('on_total')->default(false);
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_invoice', 'invoice_id');
        Migration::addForeign($table, 'account_transaction', 'rate_id');
    }
}
