<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Views\FormBuilder;
use Modules\Base\Classes\Views\ListTable;
use Modules\Base\Entities\BaseModel;

class RateDisallowedin extends BaseModel
{
    /**
     * The fields that can be filled
     * @var array<string>
     */
    protected $fillable = ['country_id', 'rate_id'];

    /**
     * List of tables names that are need in this model during migration.
     * @var array<string>
     */
    public array $migrationDependancy = ['core_country', 'account_rate'];

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "account_rate_disallowedin";

    public function listTable(): ListTable
    {
        // listing view fields
        $fields = new ListTable();

        $fields->name('country_id')->type('recordpicker')->table('core_country')->ordering(true);
        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->ordering(true);

        return $fields;

    }

    public function formBuilder(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('country_id')->type('recordpicker')->table('core_country')->group('w-1/2');
        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->group('w-1/2');

        return $fields;

    }

    public function filter(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('country_id')->type('recordpicker')->table('core_country')->group('w-1/6');
        $fields->name('rate_id')->type('recordpicker')->table('account_rate')->group('w-1/6');

        return $fields;

    }
    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $table->increments('id');
        $table->foreignId('country_id');
        $table->foreignId('rate_id');
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'core_country', 'country_id');
        Migration::addForeign($table, 'account_rate', 'rate_id');
    }
}
