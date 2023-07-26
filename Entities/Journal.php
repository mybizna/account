<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

use Modules\Core\Classes\Views\ListTable;
use Modules\Core\Classes\Views\FormBuilder;

class Journal extends BaseModel
{

    protected $fillable = ['title','grouping_id',  'partner_id', 'ledger_id',  'payment_id', 'invoice_id', 'debit', 'credit', 'params'];
    public $migrationDependancy = ['partner', 'account_ledger'];
    protected $table = "account_journal";

    protected $can_delete = "false";


    public function listTable(){
        // listing view fields
        $fields = new ListTable();

        $fields->name('title')->type('text')->ordering(true);
        $fields->name('grouping_id')->type('text')->ordering(true);
        $fields->name('partner_id')->type('recordpicker')->table('partner')->ordering(true);
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->ordering(true);
        $fields->name('payment_id')->type('recordpicker')->table('account_payment')->ordering(true);
        $fields->name('invoice_id')->type('recordpicker')->table('account_invoice')->ordering(true);
        $fields->name('debit')->type('text')->ordering(true);
        $fields->name('credit')->type('text')->ordering(true);
        $fields->name('params')->type('text')->ordering(true);


        return $fields;

    }
    
    public function formBuilder(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('title')->type('text')->group('w-1/2');
        $fields->name('grouping_id')->type('text')->group('w-1/2');
        $fields->name('partner_id')->type('recordpicker')->table('partner')->group('w-1/2');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/2');
        $fields->name('payment_id')->type('recordpicker')->table('account_payment')->group('w-1/2');
        $fields->name('invoice_id')->type('recordpicker')->table('account_invoice')->group('w-1/2');
        $fields->name('debit')->type('text')->group('w-1/2');
        $fields->name('credit')->type('text')->group('w-1/2');
        $fields->name('params')->type('text')->group('w-1/2');


        return $fields;

    }

    public function filter(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('title')->type('text')->group('w-1/6');
        $fields->name('grouping_id')->type('text')->group('w-1/6');
        $fields->name('partner_id')->type('recordpicker')->table('partner')->group('w-1/6');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/6');
        $fields->name('payment_id')->type('recordpicker')->table('account_payment')->group('w-1/6');
        $fields->name('invoice_id')->type('recordpicker')->table('account_invoice')->group('w-1/6');
        $fields->name('debit')->type('text')->group('w-1/6');
        $fields->name('credit')->type('text')->group('w-1/6');
        
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
        $table->char('grouping_id');
        $table->foreignId('partner_id');
        $table->foreignId('ledger_id');
        $table->foreignId('payment_id')->nullable();
        $table->foreignId('invoice_id')->nullable();
        $table->decimal('debit', 20, 2)->nullable();
        $table->decimal('credit', 20, 2)->nullable();
        $table->string('params')->nullable();
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'partner', 'partner_id');
        Migration::addForeign($table, 'account_ledger', 'ledger_id');
        Migration::addForeign($table, 'account_payment', 'payment_id');
        Migration::addForeign($table, 'account_invoice', 'invoice_id');
    }
}
