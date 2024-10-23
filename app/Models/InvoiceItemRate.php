<?php

namespace Modules\Account\Models;

use Modules\Account\Models\InvoiceItem;
use Modules\Account\Models\Rate;
use Modules\Base\Models\BaseModel;

class InvoiceItemRate extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'slug', 'rate_id', 'invoice_item_id', 'method', 'value', 'params', 'ordering', 'on_total',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice_item_rate";

    /**
     * Add relationship to InvoiceItem
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoiceItem()
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    /**
     * Add relationship to Rate
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rate()
    {
        return $this->belongsTo(Rate::class);
    }
}
