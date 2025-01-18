<?php

namespace Modules\Account\Models;

use Modules\Account\Models\Coupon;
use Modules\Account\Models\Invoice;
use Modules\Base\Models\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Add relationship to Coupon
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function migration(Blueprint $table): void
    {

        $table->foreignId('invoice_id')->nullable()->constrained(table: 'account_invoice');
        $table->foreignId('coupon_id')->nullable()->constrained(table: 'account_coupon');

    }
}
