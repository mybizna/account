<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Entities\BaseModel;

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
