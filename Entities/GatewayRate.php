<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;

class GatewayRate extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['gateway_id', 'rate_id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_gateway_rate";

}
