<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class Journal extends BaseModel
{

    protected $fillable = ['title', 'partner_id', 'ledger_id', 'grouping_id', 'debit', 'credit', 'params'];
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
    }
}
