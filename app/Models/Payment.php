<?php
namespace Modules\Account\Models;

use Base\Casts\Money;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Modules\Account\Models\Gateway;
use Modules\Account\Models\Ledger;
use Modules\Base\Models\BaseModel;
use Modules\Partner\Models\Partner;

class Payment extends BaseModel
{

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total' => Money::class, // Use the custom MoneyCast
    ];

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'amount', 'ledger_id', 'partner_id', 'gateway_id', 'receipt_no',
        'code', 'others', 'stage', 'status', "type", 'is_posted',
    ];

    /**
     * The fields that can be filled
     *
     * @var string
     */
    protected $table = "account_payment";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected bool $can_delete = false;

    /**
     * Set if model is visible from frontend.
     *
     * @var bool
     */
    public bool $show_frontend = true;

    /**
     * Add relationship to Ledger
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ledger(): BelongsTo
    {
        return $this->belongsTo(Ledger::class);
    }

    /**
     * Add relationship to Partner
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Add relationship to Gateway
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
    }

    /**
     * Add relationship to PaymentRate
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(PaymentRate::class);
    }

    public function migration(Blueprint $table): void
    {

        $table->string('title');
        $table->integer('amount');
        $table->string('currency')->default('USD');
        $table->unsignedBigInteger('ledger_id')->nullable();
        $table->unsignedBigInteger('partner_id')->nullable();
        $table->unsignedBigInteger('gateway_id')->nullable();
        $table->string('receipt_no')->nullable();
        $table->string('code')->nullable();
        $table->string('others')->nullable();
        $table->enum('stage', ['pending', 'wallet', 'posted'])->default('pending');
        $table->enum('status', ['pending', 'paid', 'reversed', 'canceled'])->default('pending');
        $table->enum('type', ['in', 'out'])->default('in');
        $table->tinyInteger('is_posted')->default(false);

    }

    public function post_migration(Blueprint $table): void
    {
        $table->foreign('ledger_id')->references('id')->on('account_ledger')->onDelete('set null');
        $table->foreign('partner_id')->references('id')->on('partner_partner')->onDelete('set null');
        $table->foreign('gateway_id')->references('id')->on('account_gateway')->onDelete('set null');
    }

}
