<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

class GatewayRate extends BaseModel
{

    protected $fillable = ['gateway_id', 'rate_id'];
    public $migrationDependancy = ['account_gateway', 'account_rate'];
    protected $table = "account_gateway_rate";

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $table->increments('id');
        $table->foreignId('gateway_id');
        $table->foreignId('rate_id');
    }


    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_gateway', 'gateway_id');
        Migration::addForeign($table, 'account_rate', 'rate_id');
    }
}
