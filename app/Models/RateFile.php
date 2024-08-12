<?php

namespace Modules\Account\Models;

use Modules\Base\Models\BaseModel;

class RateFile extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'rate_id', 'year', 'month', 'token', 'type', 'max_limit', 'file', 'is_processed',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_rate_file";

}
