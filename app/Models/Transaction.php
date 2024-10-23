<?php

namespace Modules\Account\Models;

use Modules\Account\Models\ChartOfAccount;
use Modules\Account\Models\Ledger;
use Modules\Account\Models\LedgerSetting;
use Modules\Base\Models\BaseModel;
use Modules\Partner\Models\Partner;

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

    /**
     * Add Relationship to Partner
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Add Relationship to LedgerSetting
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ledgerSetting()
    {
        return $this->belongsTo(LedgerSetting::class);
    }

    /**
     * Add Relationship to ChartOfAccount
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function leftChartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'left_chart_of_account_id');
    }

    /**
     * Add Relationship to Ledger
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function leftLedger()
    {
        return $this->belongsTo(Ledger::class, 'left_ledger_id');
    }

    /**
     * Add Relationship to ChartOfAccount
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rightChartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'right_chart_of_account_id');
    }

    /**
     * Add Relationship to Ledger
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rightLedger()
    {
        return $this->belongsTo(Ledger::class, 'right_ledger_id');
    }

}
