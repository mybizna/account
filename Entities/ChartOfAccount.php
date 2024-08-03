<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Entities\BaseModel;

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

    /**
     * Migration for creating table.
     *
     * @param Blueprint $table
     *
     * @return void
     */
    public function fields(Blueprint $table = null): void
    {
        $this->fields = $table ?? new Blueprint($this->table);

        $this->fields->increments('id');
        $this->fields->string('name')->nullable()->html('text');
        $this->fields->string('slug')->nullable()->html('text');
    }


}
