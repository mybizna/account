<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Entities\BaseModel;

class FinancialYear extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'start_date', 'end_date', 'description'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_financial_year";

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
     *
     * @return void
     */
    public function fields(Blueprint $table = null): void
    {
        $this->fields = $table ?? new Blueprint($this->table);

        $this->fields->increments('id')->html('hidden');
        $this->fields->string('name')->nullable()->html('text');
        $this->fields->date('start_date')->nullable()->html('datetime');
        $this->fields->date('end_date')->nullable()->html('datetime');
        $this->fields->string('description')->nullable()->html('textarea');
    }

}
