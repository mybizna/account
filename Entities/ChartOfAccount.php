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
     * The fields that are to be render when performing relationship queries.
     *
     * @var array<string>
     */
    public $rec_names = ['name'];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_chart_of_account";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected string $can_delete = "false";

    /**
     * Migration for creating table.
     *
     * @param Blueprint $table
     *
     * @return void
     */
    public function fields(Blueprint $table): void
    {
        $this->fields = $table ?? new Blueprint($this->table);

        $this->fields->increments('id');
        $this->fields->string('name')->nullable()->html('text');
        $this->fields->string('slug')->nullable()->html('text');
    }
}
