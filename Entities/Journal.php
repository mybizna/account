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
        $table->integer('partner_id');
        $table->integer('ledger_id');
        $table->integer('payment_id')->nullable();
        $table->integer('invoice_id')->nullable();
        $table->decimal('debit', 20, 2)->nullable();
        $table->decimal('credit', 20, 2)->nullable();
        $table->string('params')->nullable();
    }

    public function post_migration(Blueprint $table)
    {
        if (Migration::checkKeyExist('account_journal', 'partner_id')) {
            $table->foreign('partner_id')->references('id')->on('partner')->nullOnDelete();
        }

        if (Migration::checkKeyExist('account_journal', 'ledger_id')) {
            $table->foreign('ledger_id')->references('id')->on('account_ledger')->nullOnDelete();
        }

        if (Migration::checkKeyExist('account_journal', 'payment_id')) {
            $table->foreign('payment_id')->references('id')->on('account_payment')->nullOnDelete();
        }

        if (Migration::checkKeyExist('account_journal', 'invoice_id')) {
            $table->foreign('invoice_id')->references('id')->on('account_invoice')->nullOnDelete();
        }
    }
}
