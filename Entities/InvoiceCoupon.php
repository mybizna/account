<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

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
