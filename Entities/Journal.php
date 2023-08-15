<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class Journal extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['title', 'grouping_id', 'partner_id', 'ledger_id', 'payment_id', 'invoice_id', 'debit', 'credit', 'params'];

    /**
     * The fields that are to be render when performing relationship queries.
     *
     * @var array<string>
     */
    public $rec_names = ['title'];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = ['partner', 'account_ledger'];

    /**
     * The fields that can be filled
     *
     * @var string
     */
    protected $table = "account_journal";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected $can_delete = false;

    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     * @return void
     */
    public function fields(Blueprint $table = null): void
    {

        $this->fields = $table ?? new Blueprint($this->table);

        $this->fields->increments('id')->html('text');
        $this->fields->string('title')->html('text');
        $this->fields->char('grouping_id')->html('text');
        $this->fields->foreignId('partner_id')->html('recordpicker')->table(['partner']);
        $this->fields->foreignId('ledger_id')->html('recordpicker')->table(['account', 'ledger']);
        $this->fields->foreignId('payment_id')->nullable()->html('recordpicker')->table(['account', 'payment']);
        $this->fields->foreignId('invoice_id')->nullable()->html('recordpicker')->table(['account', 'invoice']);
        $this->fields->decimal('debit', 20, 2)->nullable()->html('amount');
        $this->fields->decimal('credit', 20, 2)->nullable()->html('amount');
        $this->fields->string('params')->nullable()->html('text');
    }

    /**
     * List of structure for this model.
     */
    public function structure($structure): array
    {
        $structure = [
            'table' => ['title', 'grouping_id', 'partner_id', 'ledger_id', 'payment_id', 'invoice_id', 'debit', 'credit'],
            'filter' => ['title', 'grouping_id', 'partner_id', 'ledger_id', 'payment_id', 'invoice_id'],
        ];

        return $structure;
    }

}
