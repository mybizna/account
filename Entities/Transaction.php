<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class Transaction extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'amount', 'description', 'partner_id', 'ledger_setting_id', 'left_chart_of_account_id', 'left_ledger_id',
        'right_chart_of_account_id', 'right_ledger_id', 'is_processed',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_transaction";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected bool $can_delete = false;

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
        $this->fields->decimal('amount', 20, 2)->default(0.00)->html('amount');
        $this->fields->string('description')->html('textarea');
        $this->fields->foreignId('partner_id')->html('recordpicker')->relation(['partner']);
        $this->fields->foreignId('ledger_setting_id')->nullable()->html('recordpicker')->relation(['account', 'ledger_setting']);
        $this->fields->foreignId('left_chart_of_account_id')->nullable()->html('recordpicker')->relation(['account', 'chart_of_account']);
        $this->fields->foreignId('left_ledger_id')->nullable()->html('recordpicker')->relation(['account', 'ledger']);
        $this->fields->foreignId('right_chart_of_account_id')->nullable()->html('recordpicker')->relation(['account', 'chart_of_account']);
        $this->fields->foreignId('right_ledger_id')->nullable()->html('recordpicker')->relation(['account', 'ledger']);
        $this->fields->tinyInteger('is_processed')->nullable()->html('switch');
    }




}
