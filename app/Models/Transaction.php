<?php

namespace Modules\Account\Models;

use Modules\Base\Models\BaseModel;

class Transaction extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'amount', 'description', 'partner_id', 'ledger_setting_id', 'left_chart_of_account_id', 'left_ledger_id',
        'right_chart_of_account_id', 'right_ledger_id', 'is_processed',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_transaction";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected bool $can_delete = false;

}
