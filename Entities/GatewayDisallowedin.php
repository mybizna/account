<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

use Modules\Core\Classes\Views\ListTable;
use Modules\Core\Classes\Views\FormBuilder;

class GatewayDisallowedin extends BaseModel
{

    protected $fillable = ['country_id', 'gateway_id'];
    public $migrationDependancy = ['core_country', 'account_gateway'];
    protected $table = "account_gateway_disallowedin";


    public function listTable(){
        // listing view fields
        $fields = new ListTable();

        $fields->name('country_id')->type('recordpicker')->table('core_country')->ordering(true);
        $fields->name('gateway_id')->type('recordpicker')->table('account_gateway')->ordering(true);

        return $fields;

    }
    
    public function formBuilder(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('country_id')->type('recordpicker')->table('core_country')->group('w-1/2');
        $fields->name('gateway_id')->type('recordpicker')->table('account_gateway')->group('w-1/2');

        return $fields;

    }

    public function filter(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('country_id')->type('recordpicker')->table('core_country')->group('w-1/2');
        $fields->name('gateway_id')->type('recordpicker')->table('account_gateway')->group('w-1/2');

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
        $table->foreignId('country_id');
        $table->foreignId('gateway_id');
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'core_country', 'country_id');
        Migration::addForeign($table, 'account_gateway', 'gateway_id');
    }
}
