<?php

namespace Modules\Account\Models;

use Modules\Base\Models\BaseModel;

class Coupon extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'code', 'description', 'value', 'start_amount', 'end_amount',
        'start_date', 'end_date', 'applied', 'is_percent', 'published', 'is_visible',
    ];

    /**
     * The this->fields associated with the model.
     *
     * @var string
     */
    protected $table = "account_coupon";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected bool $can_delete = false;
}
