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
    public array $migrationDependancy = ['account_chart_of_account'];

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

    /**
     * List of structure for this model.
     */
    public function structure($structure): array
    {
        $structure['table'] = ['name', 'slug', 'chart_id', 'parent_id', 'is_system'];
        $structure['form'] = [
            ['label' => 'Ledger Category Name', 'class' => 'col-span-full', 'fields' => ['name']],
            ['label' => 'Ledger Category Details', 'class' => 'col-span-full  md:col-span-6 md:pr-2', 'fields' => ['slug', 'chart_id', 'parent_id']],
            ['label' => 'Ledger Category Other Setting', 'class' => 'col-span-full  md:col-span-6 md:pr-2', 'fields' => ['is_system']],
        ];
        $structure['filter'] = ['name', 'slug', 'chart_id'];

        return $structure;
    }
    
    /**
     * Define rights for this model.
     * 
     * @return array
     */
    public function rights(): array
    {
        $rights = parent::rights();

        $rights['staff'] = [];
        $rights['registered'] = [];
        $rights['guest'] = [];

        return $rights;
    }

}
