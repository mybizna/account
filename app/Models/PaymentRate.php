<?php
namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Modules\Account\Models\Payment;
use Modules\Account\Models\Rate;
use Modules\Base\Models\BaseModel;

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

        $table->unsignedBigInteger('payment_id')->nullable();
        $table->unsignedBigInteger('rate_id')->nullable();
    }

    public function post_migration(Blueprint $table): void
    {
        $table->foreign('payment_id')->references('id')->on('account_payment')->onDelete('set null');
        $table->foreign('rate_id')->references('id')->on('account_rate')->onDelete('set null');
    }

}
