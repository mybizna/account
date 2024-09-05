<?php

namespace Modules\Account\Models;

use Modules\Account\Models\Ledger;
use Modules\Base\Models\BaseModel;
use Modules\Core\Models\Currency;

class Gateway extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'slug', 'ledger_id', 'currency_id', 'image', 'url', 'instruction',
        'module', 'ordering', 'is_default', 'is_hidden', 'is_hide_in_invoice', 'published',

    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_gateway";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected bool $can_delete = false;

    /**
     * Add relationship to Ledger
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    /**
     * Add relationship to Currency
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

}
