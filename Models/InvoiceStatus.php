<?php

namespace Modules\Account\Models;

use Modules\Base\Models\BaseModel;

class InvoiceStatus extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['type_name', 'slug'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice_status";

}
