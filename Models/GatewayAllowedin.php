<?php

namespace Modules\Account\Models;

use Modules\Base\Models\BaseModel;

class GatewayAllowedin extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['country_id', 'gateway_id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_gateway_allowedin";

}
