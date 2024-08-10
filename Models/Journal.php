<?php

namespace Modules\Account\Models;

use Modules\Base\Models\BaseModel;

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

}
