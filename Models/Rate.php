<?php

namespace Modules\Account\Models;

use Modules\Base\Models\BaseModel;

class Rate extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'slug', 'value', 'ledger_id',
        'value', 'method', 'params', 'ordering',
        'on_total', 'published',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_rate";

}
