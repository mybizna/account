<?php
namespace Modules\Account\Models;

use Base\Casts\Money;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Modules\Account\Models\Invoice;
use Modules\Account\Models\Ledger;
use Modules\Account\Models\Payment;
use Modules\Base\Models\BaseModel;
use Modules\Partner\Models\Partner;

class Journal extends BaseModel
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
    protected $fillable = ['title', 'grouping_id', 'partner_id', 'ledger_id', 'payment_id', 'invoice_id', 'debit', 'credit', 'params'];

    /**
     * The fields that can be filled
     *
     * @var string
     */
    protected $table = "account_journal";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected bool $can_delete = false;

    /**
     * Add relationship to Partner
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Add relationship to Ledger
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ledger(): BelongsTo
    {
        return $this->belongsTo(Ledger::class);
    }

    /**
     * Add relationship to Payment
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Add relationship to Invoice
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function migration(Blueprint $table): void
    {

        $table->string('title');
        $table->char('grouping_id');
        $table->unsignedBigInteger('partner_id')->nullable();
        $table->unsignedBigInteger('ledger_id')->nullable();
        $table->unsignedBigInteger('payment_id')->nullable();
        $table->unsignedBigInteger('invoice_id')->nullable();
        $table->integer('debit')->nullable();
        $table->integer('credit')->nullable();
        $table->string('currency')->default('USD');
        $table->string('params')->nullable();

    }

    public function post_migration(Blueprint $table): void
    {
        $table->foreign('partner_id')->references('id')->on('partner_partner')->onDelete('set null');
        $table->foreign('ledger_id')->references('id')->on('account_ledger')->onDelete('set null');
        $table->foreign('payment_id')->references('id')->on('account_payment')->onDelete('set null');
        $table->foreign('invoice_id')->references('id')->on('account_invoice')->onDelete('set null');
    }

}
