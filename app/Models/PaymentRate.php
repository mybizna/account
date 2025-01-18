<?php

namespace Modules\Account\Models;

use Modules\Account\Models\Payment;
use Modules\Account\Models\Rate;
use Modules\Base\Models\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRate extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['payment_id', 'rate_id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_payment_rate";

    /**
     * Add relationship to Payment
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Add relationship to Rate
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class);
    }
    public function migration(Blueprint $table): void
    {

        $table->foreignId('payment_id')->nullable()->constrained(table: 'account_payment')->onDelete('set null');
        $table->foreignId('rate_id')->nullable()->constrained(table: 'account_rate')->onDelete('set null');

    }

}
