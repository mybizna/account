<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;

class InvoiceItem extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'invoice_id', 'ledger_id', 'price', 'amount', 'quantity',
        'module', 'model', 'item_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice_item";

}
