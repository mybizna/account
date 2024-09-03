<?php

namespace Modules\Account\Models;

use Modules\Base\Models\BaseModel;

class InvoiceCoupon extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['invoice_id', 'coupon_id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice_coupon";
}
