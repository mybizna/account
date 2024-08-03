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



}
