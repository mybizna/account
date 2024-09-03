<?php

namespace Modules\Account\Models;

use Modules\Base\Models\BaseModel;

class RateDisallowedin extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['country_id', 'rate_id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_rate_disallowedin";
}
