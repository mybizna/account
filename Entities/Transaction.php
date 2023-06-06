<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

class Transaction extends BaseModel
{

    protected $fillable = [
        'amount', 'description', 'partner_id', 'ledger_setting_id', 'left_chart_of_account_id', 'left_ledger_id',
        'right_chart_of_account_id', 'right_ledger_id', 'is_processed'
    ];
    public $migrationDependancy = ['partner', 'account_payment', 'account_rate'];
    protected $table = "account_transaction";

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
