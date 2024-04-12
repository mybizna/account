<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class GatewayAllowedin extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['country_id', 'gateway_id'];

    /**
     * The fields that are to be render when performing relationship queries.
     *
     * @var array<string>
     */
    public $rec_names = ['country_id', 'gateway_id'];

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
    protected $table = "account_gateway_allowedin";

    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     *
     * @return void
     */
    public function fields(Blueprint $table = null): void
    {
        $this->fields = $table ?? new Blueprint($this->table);

        $this->fields->increments('id')->html('hidden');
        $this->fields->foreignId('country_id')->html('recordpicker')->relation(['core', 'country']);
        $this->fields->foreignId('gateway_id')->html('recordpicker')->relation(['account', 'gateway']);
    }

    /**
     * Define rights for this model.
     *
     * @return array
     */
    public function rights(): array
    {

    }

}
