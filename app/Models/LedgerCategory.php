<?php

namespace Modules\Account\Models;

use Modules\Account\Models\ChartOfAccount;
use Modules\Account\Models\Ledger;
use Modules\Account\Models\LedgerCategory;
use Modules\Base\Models\BaseModel;

class LedgerCategory extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'slug', 'chart_id', 'parent_id', 'is_system'];

    /**
     * The fields that can be filled
     *
     * @var string
     */
    protected $table = "account_ledger_category";

    /**
     * Add relationship to Ledger
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function ledgers()
    {
        return $this->hasMany(Ledger::class);
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
     * Add relationship to Parent
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function parent()
    {
        return $this->belongsTo(LedgerCategory::class, 'parent_id');
    }

}
