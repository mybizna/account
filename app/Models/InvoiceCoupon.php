<?php

namespace Modules\Account\Models;

use Modules\Account\Models\Coupon;
use Modules\Account\Models\Invoice;
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

    /**
     * Add relationship to Invoice
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Add relationship to Coupon
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
