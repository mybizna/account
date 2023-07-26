<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

use Modules\Core\Classes\Views\ListTable;
use Modules\Core\Classes\Views\FormBuilder;

class Transaction extends BaseModel
{

    protected $fillable = [
        'amount', 'description', 'partner_id', 'ledger_setting_id', 'left_chart_of_account_id', 'left_ledger_id',
        'right_chart_of_account_id', 'right_ledger_id', 'is_processed'
    ];
    public $migrationDependancy = ['partner', 'account_payment', 'account_rate'];
    protected $table = "account_transaction";

    protected $can_delete = "false";


    public function listTable(){
        // listing view fields
        $fields = new ListTable();

        $fields->name('amount')->type('text')->ordering(true);
        $fields->name('partner_id')->type('recordpicker')->table('partner')->ordering(true);
        $fields->name('ledger_setting_id')->type('recordpicker')->table('account_ledger_setting')->ordering(true);
        $fields->name('left_chart_of_account_id')->type('recordpicker')->table('account_chart_of_account')->ordering(true);
        $fields->name('left_ledger_id')->type('recordpicker')->table('account_ledger')->ordering(true);
        $fields->name('right_chart_of_account_id')->type('recordpicker')->table('account_chart_of_account')->ordering(true);
        $fields->name('right_ledger_id')->type('recordpicker')->table('account_ledger')->ordering(true);
        $fields->name('is_processed')->type('switch')->ordering(true);


        return $fields;

    }
    
    public function formBuilder(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('amount')->type('text')->group('w-1/2');
        $fields->name('partner_id')->type('recordpicker')->table('partner')->group('w-1/2');
        $fields->name('ledger_setting_id')->type('recordpicker')->table('account_ledger_setting')->group('w-1/2');
        $fields->name('left_chart_of_account_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/2');
        $fields->name('left_ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/2');
        $fields->name('right_chart_of_account_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/2');
        $fields->name('right_ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/2');
        $fields->name('is_processed')->type('switch')->group('w-1/2');
        $fields->name('description')->type('text')->group('w-full');


        return $fields;

    }

    public function filter(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('amount')->type('text')->group('w-1/6');
        $fields->name('partner_id')->type('recordpicker')->table('partner')->group('w-1/6');
        $fields->name('ledger_setting_id')->type('recordpicker')->table('account_ledger_setting')->group('w-1/6');
        $fields->name('left_chart_of_account_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/6');
        $fields->name('left_ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/6');
        $fields->name('right_chart_of_account_id')->type('recordpicker')->table('account_chart_of_account')->group('w-1/6');
        $fields->name('right_ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/6');
        $fields->name('is_processed')->type('switch')->group('w-1/6');

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
        $table->decimal('amount', 20, 2)->default(0.00);
        $table->string('description');
        $table->foreignId('partner_id');
        $table->foreignId('ledger_setting_id')->nullable();
        $table->foreignId('left_chart_of_account_id')->nullable();
        $table->foreignId('left_ledger_id')->nullable();
        $table->foreignId('right_chart_of_account_id')->nullable();
        $table->foreignId('right_ledger_id')->nullable();
        $table->tinyInteger('is_processed')->nullable();
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_ledger_setting', 'ledger_setting_id');
        Migration::addForeign($table, 'partner', 'partner_id');
        Migration::addForeign($table, 'account_ledger', 'left_ledger_id');
        Migration::addForeign($table, 'account_chart_of_account', 'right_chart_of_account_id');
        Migration::addForeign($table, 'account_ledger', 'right_ledger_id');
    }
}
