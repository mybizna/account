<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class PaymentRate extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['payment_id', 'rate_id'];

    /**
     * The fields that are to be render when performing relationship queries.
     *
     * @var array<string>
     */
    public $rec_names = ['payment_id', 'rate_id'];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = ['account_payment', 'account_rate'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_payment_rate";

    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     * @return void
     */
    public function fields(Blueprint $table = null): void
    {
        $this->fields = $table ?? new Blueprint($this->table);

        $this->fields->increments('id')->html('hidden');
        $this->fields->foreignId('payment_id')->html('recordpicker')->relation(['account', 'payment']);
        $this->fields->foreignId('rate_id')->html('recordpicker')->relation(['account', 'rate']);
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
