<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
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

    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     * @return void
     */
    public function fields(Blueprint $table = null): void
    {

        $this->fields = $table ?? new Blueprint($this->table);

        $this->fields->increments('id')->html('hidden');
        $this->fields->string('name')->nullable()->html('text');
        $this->fields->string('slug')->nullable()->html('text');
        $this->fields->foreignId('chart_id')->nullable()->html('recordpicker')->relation(['account', 'chart_of_account']);
        $this->fields->foreignId('parent_id')->nullable()->html('recordpicker')->relation(['account', 'ledger_category']);
        $this->fields->tinyInteger('is_system')->nullable()->html('switch');
    }

 
    


}
