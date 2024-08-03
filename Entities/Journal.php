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
        $this->fields->string('title')->html('text');
        $this->fields->char('grouping_id')->html('text');
        $this->fields->foreignId('partner_id')->html('recordpicker')->relation(['partner']);
        $this->fields->foreignId('ledger_id')->html('recordpicker')->relation(['account', 'ledger']);
        $this->fields->foreignId('payment_id')->nullable()->html('recordpicker')->relation(['account', 'payment']);
        $this->fields->foreignId('invoice_id')->nullable()->html('recordpicker')->relation(['account', 'invoice']);
        $this->fields->decimal('debit', 20, 2)->nullable()->html('amount');
        $this->fields->decimal('credit', 20, 2)->nullable()->html('amount');
        $this->fields->string('params')->nullable()->html('text');
    }

 



}
