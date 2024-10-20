<?php

namespace Modules\Account\Models;

use Modules\Account\Models\Ledger;
use Modules\Account\Models\RateAllowedin;
use Modules\Account\Models\RateDisallowedin;
use Modules\Account\Models\RateFile;
use Modules\Base\Models\BaseModel;

class Rate extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'slug', 'value', 'ledger_id',
        'value', 'method', 'params', 'ordering',
        'on_total', 'published',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_rate";

    /**
     * Add relationship to Ledger
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    /**
     * Add relationship to RateFile
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(RateFile::class);
    }

    /**
     * Add relationship to RateAllowedin
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allowedins()
    {
        return $this->hasMany(RateAllowedin::class);
    }

    /**
     * Add relationship to RateDisallowedin
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function disallowedins()
    {
        return $this->hasMany(RateDisallowedin::class);
    }
}
