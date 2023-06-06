<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

class PaymentRate extends BaseModel
{

    protected $fillable = ['payment_id', 'rate_id'];
    public $migrationDependancy = ['account_payment', 'account_rate'];
    protected $table = "account_payment_rate";

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $table->increments('id');
        $table->foreignId('payment_id');
        $table->foreignId('rate_id');
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_payment', 'payment_id');
        Migration::addForeign($table, 'account_rate', 'rate_id');
    }
}
