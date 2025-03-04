<?php

namespace Modules\Account\Models;

use Modules\Base\Models\BaseModel;
use Illuminate\Database\Schema\Blueprint;

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

    public function migration(Blueprint $table): void
    {
        $table->string('name')->nullable();
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->string('description')->nullable();
    }
}
