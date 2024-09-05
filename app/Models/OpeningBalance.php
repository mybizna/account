<?php

namespace Modules\Account\Models;

use Modules\Account\Models\ChartOfAccount;
use Modules\Account\Models\FinancialYear;
use Modules\Account\Models\Ledger;
use Modules\Base\Models\BaseModel;

class OpeningBalance extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['financial_year_id', 'chart_id', 'ledger_id', 'type', 'debit', 'credit'];

    /**
     * The fields that can be filled
     *
     * @var string
     */
    protected $table = "account_opening_balance";

    /**
     * Add relationship to Ledger
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    /**
     * Add relationship to ChartOfAccount
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chart()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    /**
     * Add relationship to FinancialYear
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class);
    }
}
