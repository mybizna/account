<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class Journal extends BaseModel
{

    protected $fillable = ['title','grouping_id',  'partner_id', 'ledger_id',  'payment_id', 'invoice_id', 'debit', 'credit', 'params'];
    public $migrationDependancy = ['partner', 'account_ledger'];
    protected $table = "account_journal";

    protected $can_delete = "false";

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
