<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class OpeningBalance extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['financial_year_id', 'chart_id', 'ledger_id', 'type', 'debit', 'credit'];

    /**
     * The fields that can be filled
     *
     * @var string
     */
    protected $table = "account_opening_balance";

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
        $this->fields->foreignId('financial_year_id')->nullable()->html('recordpicker')->relation(['account', 'financial_year']);
        $this->fields->foreignId('chart_id')->nullable()->html('recordpicker')->relation(['account', 'chart_of_account']);
        $this->fields->foreignId('ledger_id')->nullable()->html('recordpicker')->relation(['account', 'ledger']);
        $this->fields->string('type', 50)->nullable()->html('number');
        $this->fields->decimal('debit', 20, 2)->default(0.00)->html('amount');
        $this->fields->decimal('credit', 20, 2)->default(0.00)->html('amount');
    }





}
