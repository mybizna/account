<?php

namespace Modules\Account\Models;

use Modules\Base\Models\BaseModel;
use Illuminate\Database\Schema\Blueprint;

class ChartOfAccount extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */

    protected $fillable = ['name', 'slug'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_chart_of_account";

    public function migration(Blueprint $table): void
    {
        $table->string('name')->nullable();
        $table->string('slug')->nullable();
    }


}
