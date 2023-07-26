<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

use Modules\Core\Classes\Views\ListTable;
use Modules\Core\Classes\Views\FormBuilder;

class InvoiceItem extends BaseModel
{

    protected $fillable = [
        'title', 'invoice_id', 'ledger_id', 'price', 'amount', 'quantity',
        'module', 'model', 'item_id',
    ];
    public $migrationDependancy = ['account_invoice', 'account_transaction'];
    protected $table = "account_invoice_item";


    public function listTable(){
        // listing view fields
        $fields = new ListTable();

        $fields->name('title')->type('text')->ordering(true);
        $fields->name('invoice_id')->type('recordpicker')->table('account_invoice')->ordering(true);
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->ordering(true);
        $fields->name('price')->type('text')->ordering(true);
        $fields->name('amount')->type('text')->ordering(true);
        $fields->name('module')->type('text')->ordering(true);
        $fields->name('model')->type('text')->ordering(true);
        $fields->name('item_id')->type('text')->ordering(true);
        $fields->name('quantity')->type('text')->ordering(true);


        return $fields;

    }
    
    public function formBuilder(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('title')->type('text')->group('w-1/2');
        $fields->name('invoice_id')->type('recordpicker')->table('account_invoice')->group('w-1/2');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/2');
        $fields->name('price')->type('text')->group('w-1/2');
        $fields->name('amount')->type('text')->group('w-1/2');
        $fields->name('module')->type('text')->group('w-1/2');
        $fields->name('model')->type('text')->group('w-1/2');
        $fields->name('item_id')->type('text')->group('w-1/2');
        $fields->name('quantity')->type('text')->group('w-1/2');


        return $fields;

    }

    public function filter(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('title')->type('text')->group('w-1/6');
        $fields->name('invoice_id')->type('recordpicker')->table('account_invoice')->group('w-1/6');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/6');
        $fields->name('price')->type('text')->group('w-1/6');
        $fields->name('amount')->type('text')->group('w-1/6');
        $fields->name('module')->type('text')->group('w-1/6');
        $fields->name('model')->type('text')->group('w-1/6');
        $fields->name('quantity')->type('text')->group('w-1/6');

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
        $table->foreignId('invoice_id');
        $table->foreignId('ledger_id');
        $table->decimal('price', 20, 2)->default(0.00);
        $table->decimal('amount', 20, 2)->default(0.00);
        $table->string('module')->nullable();
        $table->string('model')->nullable();
        $table->foreignId('item_id')->nullable();
        $table->integer('quantity')->nullable();
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_invoice', 'invoice_id');
        Migration::addForeign($table, 'account_transaction', 'transaction_id');
        Migration::addForeign($table, 'account_ledger', 'ledger_id');
    }
}
