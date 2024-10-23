<?php

namespace Modules\Account\Models;

use Modules\Account\Models\Invoice;
use Modules\Account\Models\Ledger;
use Modules\Account\Models\Payment;
use Modules\Base\Models\BaseModel;
use Modules\Partner\Models\Partner;

class Journal extends BaseModel
{

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
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Add relationship to Ledger
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    /**
     * Add relationship to Payment
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Add relationship to Invoice
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

}
