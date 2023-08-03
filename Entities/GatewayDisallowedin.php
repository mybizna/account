<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Views\FormBuilder;
use Modules\Base\Classes\Views\ListTable;
use Modules\Base\Entities\BaseModel;

class GatewayDisallowedin extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['country_id', 'gateway_id'];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = ['core_country', 'account_gateway'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_gateway_disallowedin";

    /**
     * Function for defining list of fields in table view.
     *
     * @return ListTable
     */
    public function listTable(): ListTable
    {
        // listing view fields
        $fields = new ListTable();

        $fields->name('country_id')->type('recordpicker')->table('core_country')->ordering(true);
        $fields->name('gateway_id')->type('recordpicker')->table('account_gateway')->ordering(true);

        return $fields;

    }

    /**
     * Function for defining list of fields in form view.
     * 
     * @return FormBuilder
     */
    public function formBuilder(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('country_id')->type('recordpicker')->table('core_country')->group('w-1/2');
        $fields->name('gateway_id')->type('recordpicker')->table('account_gateway')->group('w-1/2');

        return $fields;

    }

    /**
     * Function for defining list of fields used for filtering.
     * 
     * @return FormBuilder
     */
    public function filter(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('country_id')->type('recordpicker')->table('core_country')->group('w-1/2');
        $fields->name('gateway_id')->type('recordpicker')->table('account_gateway')->group('w-1/2');

        return $fields;

    }
    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table): void
    {
        $table->increments('id');
        $table->foreignId('country_id');
        $table->foreignId('gateway_id');
    }

    /**
     * Handle post migration processes for adding foreign keys.
     *
     * @param Blueprint $table
     *
     * @return void
     */
    public function post_migration(Blueprint $table): void
    {
        Migration::addForeign($table, 'core_country', 'country_id');
        Migration::addForeign($table, 'account_gateway', 'gateway_id');
    }
}
