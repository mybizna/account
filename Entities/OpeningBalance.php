<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

use Modules\Core\Classes\Views\ListTable;
use Modules\Core\Classes\Views\FormBuilder;

class OpeningBalance extends BaseModel
{

    protected $fillable = ['financial_year_id', 'chart_id', 'ledger_id', 'type', 'debit', 'credit'];
    public $migrationDependancy = ['account_ledger', 'account_chart_of_account', 'account_financial_year'];
    protected $table = "account_opening_balance";


    public function listTable(){
        // listing view fields
        $fields = new ListTable();

        $fields->name('financial_year_id')->type('recordpicker')->table('account_financial_year')->ordering(true);
        $fields->name('chart_id')->type('recordpicker')->table('account_chart_of_account')->ordering(true);
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->ordering(true);
        $fields->name('type')->type('text')->ordering(true);
        $fields->name('debit')->type('text')->ordering(true);
        $fields->name('credit')->type('text')->ordering(true);


        return $fields;

    }
    
    public function formBuilder(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('financial_year_id')->type('recordpicker')->table('account_financial_year')->group('w-1/2');
        $fields->name('chart_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/2');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/2');
        $fields->name('type')->type('text')->group('w-1/2');
        $fields->name('debit')->type('text')->group('w-1/2');
        $fields->name('credit')->type('text')->group('w-1/2');


        return $fields;

    }

    public function filter(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('financial_year_id')->type('recordpicker')->table('account_financial_year')->group('w-1/6');
        $fields->name('chart_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/6');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/6');
        $fields->name('type')->type('text')->group('w-1/6');
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
        $table->foreignId('financial_year_id')->nullable();
        $table->foreignId('chart_id')->nullable();
        $table->foreignId('ledger_id')->nullable();
        $table->string('type', 50)->nullable();
        $table->decimal('debit', 20, 2)->default(0.00);
        $table->decimal('credit', 20, 2)->default(0.00);
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_financial_year', 'financial_year_id');
        Migration::addForeign($table, 'account_chart_of_account', 'chart_id');
        Migration::addForeign($table, 'account_ledger', 'ledger_id');
    }
}
