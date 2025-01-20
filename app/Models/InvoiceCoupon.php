<?php
namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
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

        $table->unsignedBigInteger('invoice_id')->nullable();
        $table->unsignedBigInteger('coupon_id')->nullable();

    }

    public function post_migration(Blueprint $table): void
    {
        $table->foreign('invoice_id')->references('id')->on('account_invoice')->onDelete('set null');
        $table->foreign('coupon_id')->references('id')->on('account_coupon')->onDelete('set null');
    }
}
