<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;

class GatewayDisallowedin extends BaseModel
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
    protected $table = "account_gateway_disallowedin";

}
