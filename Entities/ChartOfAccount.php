<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Entities\BaseModel;

class ChartOfAccount extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'slug'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_chart_of_account";

 

}
