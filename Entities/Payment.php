<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

use Modules\Core\Classes\Views\ListTable;
use Modules\Core\Classes\Views\FormBuilder;

class Payment extends BaseModel
{

    protected $fillable = [
        'title', 'amount', 'ledger_id', 'partner_id', 'gateway_id', 'receipt_no',
        'code', 'others', 'stage', 'status', "type", 'is_posted',
    ];
    public $migrationDependancy = ['account_gateway', 'account_ledger', 'partner'];
    protected $table = "account_payment";

    protected $can_delete = "false";


    public function listTable(){
        // listing view fields
        $fields = new ListTable();

        $fields->name('title')->type('text')->ordering(true);
        $fields->name('amount')->type('text')->ordering(true);
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->ordering(true);
        $fields->name('partner_id')->type('recordpicker')->table('partner')->ordering(true);
        $fields->name('gateway_id')->type('recordpicker')->table('account_gateway')->ordering(true);
        $fields->name('receipt_no')->type('text')->ordering(true);
        $fields->name('code')->type('text')->ordering(true);
        $fields->name('others')->type('text')->ordering(true);
        $fields->name('stage')->type('text')->ordering(true);
        $fields->name('status')->type('switch')->ordering(true);
        $fields->name('type')->type('text')->ordering(true);
        $fields->name('is_posted')->type('switch')->ordering(true);


        return $fields;

    }
    
    public function formBuilder(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('title')->type('text')->group('w-1/2');
        $fields->name('amount')->type('text')->group('w-1/2');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/2');
        $fields->name('partner_id')->type('recordpicker')->table('partner')->group('w-1/2');
        $fields->name('gateway_id')->type('recordpicker')->table('account_gateway')->group('w-1/2');
        $fields->name('receipt_no')->type('text')->group('w-1/2');
        $fields->name('code')->type('text')->group('w-1/2');
        $fields->name('others')->type('text')->group('w-1/2');
        $fields->name('stage')->type('text')->group('w-1/2');
        $fields->name('status')->type('switch')->group('w-1/2');
        $fields->name('type')->type('text')->group('w-1/2');
        $fields->name('is_posted')->type('switch')->group('w-1/2');


        return $fields;

    }

    public function filter(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('title')->type('text')->group('w-1/6');
        $fields->name('receipt_no')->type('text')->group('w-1/6');
        $fields->name('code')->type('text')->group('w-1/6');
        $fields->name('amount')->type('text')->group('w-1/6');
        $fields->name('ledger_id')->type('recordpicker')->table('account_ledger')->group('w-1/6');
        $fields->name('partner_id')->type('recordpicker')->table('partner')->group('w-1/6');
        $fields->name('gateway_id')->type('recordpicker')->table('account_gateway')->group('w-1/6');

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
        $table->decimal('amount', 20, 2);
        $table->foreignId('ledger_id');
        $table->foreignId('partner_id');
        $table->foreignId('gateway_id');
        $table->string('receipt_no')->nullable();
        $table->string('code')->nullable();
        $table->string('others')->nullable();
        $table->enum('stage', ['pending', 'wallet', 'posted'])->default('pending');
        $table->enum('status', ['pending', 'paid', 'reversed', 'canceled'])->default('pending');
        $table->enum('type', ['in', 'out'])->default('in');
        $table->tinyInteger('is_posted')->default(false);
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_gateway', 'gateway_id');
        Migration::addForeign($table, 'account_ledger', 'ledger_id');
        Migration::addForeign($table, 'account_invoice', 'invoice_id');
    }
}
