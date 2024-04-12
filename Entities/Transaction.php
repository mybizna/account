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
     * The fields that are to be render when performing relationship queries.
     *
     * @var array<string>
     */
    public $rec_names = ['description', 'amount'];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = ['partner', 'account_payment', 'account_rate'];

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

    /**
     * List of structure for this model.
     */
    public function structure($structure): array
    {
        $structure['table'] = ['amount', 'partner_id', 'ledger_setting_id', 'left_chart_of_account_id', 'left_ledger_id', 'right_chart_of_account_id', 'right_ledger_id', 'is_processed'];
        $structure['form'] = [
            ['label' => 'Transaction Left Move ', 'class' => 'col-span-full  md:col-span-6 md:pr-2', 'fields' => ['left_chart_of_account_id', 'left_ledger_id']],
            ['label' => 'Transaction Right Move ', 'class' => 'col-span-full  md:col-span-6 md:pr-2', 'fields' => ['right_chart_of_account_id', 'right_ledger_id']],
            ['label' => 'Transaction Details', 'class' => 'col-span-full  md:col-span-6 md:pr-2', 'fields' => ['amount', 'partner_id', 'ledger_setting_id']],
            ['label' => 'Transaction Setting', 'class' => 'col-span-full  md:col-span-6 md:pr-2', 'fields' => ['is_processed']],
        ];
        $structure['filter'] = ['partner_id', 'ledger_setting_id', 'left_chart_of_account_id', 'left_ledger_id', 'right_chart_of_account_id', 'right_ledger_id'];

        return $structure;
    }

    /**
     * Define rights for this model.
     *
     * @return array
     */
    public function rights(): array
    {
        $rights = parent::rights();

        $rights['staff'] = ['view' => true];
        $rights['registered'] = ['view' => true];
        $rights['guest'] = [];

        return $rights;
    }

}
