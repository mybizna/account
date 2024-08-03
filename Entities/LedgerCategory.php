<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;

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

}
