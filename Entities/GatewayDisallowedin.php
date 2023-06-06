<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

class GatewayDisallowedin extends BaseModel
{

    protected $fillable = ['country_id', 'gateway_id'];
    public $migrationDependancy = ['core_country', 'account_gateway'];
    protected $table = "account_gateway_disallowedin";

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $table->increments('id');
        $table->foreignId('country_id');
        $table->foreignId('gateway_id');
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'core_country', 'country_id');
        Migration::addForeign($table, 'account_gateway', 'gateway_id');
    }
}
