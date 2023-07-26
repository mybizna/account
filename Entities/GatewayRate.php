<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

use Modules\Core\Classes\Views\ListTable;
use Modules\Core\Classes\Views\FormBuilder;

class GatewayRate extends BaseModel
{

    protected $fillable = ['gateway_id', 'rate_id'];
    public $migrationDependancy = ['account_gateway', 'account_rate'];
    protected $table = "account_gateway_rate";


    public function listTable(){
        // listing view fields
        $fields = new ListTable();

        $fields->name('gateway_id')->type('recordpicker')->table('account_gateway')->ordering(true);
        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->ordering(true);

        return $fields;

    }
    
    public function formBuilder(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('gateway_id')->type('recordpicker')->table('account_gateway')->group('w-1/2');
        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->group('w-1/2');

        return $fields;

    }

    public function filter(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('gateway_id')->type('recordpicker')->table('account_gateway')->group('w-1/2');
        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->group('w-1/2');

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
        $table->foreignId('gateway_id');
        $table->foreignId('rate_id');
    }


    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_gateway', 'gateway_id');
        Migration::addForeign($table, 'account_rate', 'rate_id');
    }
}
