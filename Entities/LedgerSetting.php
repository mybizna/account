<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

class LedgerSetting extends BaseModel
{

    protected $fillable = ['title', 'slug','left_chart_of_account_id', 'left_ledger_id', 'right_chart_of_account_id', 'right_ledger_id'];
    public $migrationDependancy = ['account_ledger'];
    protected $table = "account_ledger_setting";

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {

        $table->increments('id');
        $table->string('title')->nullable();
        $table->string('slug')->nullable();
        $table->integer('left_chart_of_account_id')->nullable();
        $table->integer('left_ledger_id')->nullable();
        $table->integer('right_chart_of_account_id')->nullable();
        $table->integer('right_ledger_id')->nullable();
    }

    public function post_migration(Blueprint $table)
    {

        if (Migration::checkKeyExist('account_ledger_setting', 'left_chart_of_account_id')) {
            $table->foreign('left_chart_of_account_id')->references('id')->on('account_chart_of_account')->nullOnDelete();
        }

        if (Migration::checkKeyExist('account_ledger_setting', 'left_ledger_id')) {
            $table->foreign('left_ledger_id')->references('id')->on('account_ledger')->nullOnDelete();
        }

        if (Migration::checkKeyExist('account_ledger_setting', 'right_chart_of_account_id')) {
            $table->foreign('right_chart_of_account_id')->references('id')->on('account_chart_of_account')->nullOnDelete();
        }

        if (Migration::checkKeyExist('account_ledger_setting', 'right_ledger_id')) {
            $table->foreign('right_ledger_id')->references('id')->on('account_ledger')->nullOnDelete();
        }
    }
}
