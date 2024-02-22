<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

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
     * The fields that are to be render when performing relationship queries.
     *
     * @var array<string>
     */
    public $rec_names = ['title', 'value'];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = ['account_ledger'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_rate";

    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     * @return void
     */
    public function fields(Blueprint $table = null): void
    {
        $this->fields = $table ?? new Blueprint($this->table);

        $methods = ['+' => '+', '+%' => '+%', '-' => '-', '-%' => '-%'];

        $this->fields->increments('id')->html('hidden');
        $this->fields->string('title')->html('text');
        $this->fields->string('slug')->html('text');
        $this->fields->foreignId('ledger_id')->html('recordpicker')->relation(['account', 'ledger']);
        $this->fields->decimal('value', 20, 2)->html('amount');
        $this->fields->enum('method', array_keys($methods))->default('+')->html('select');
        $this->fields->string('params')->nullable()->html('textarea');
        $this->fields->tinyInteger('ordering')->nullable()->html('text');
        $this->fields->tinyInteger('on_total')->default(false)->html('switch');
        $this->fields->tinyInteger('published')->default(false)->html('switch');
    }

    /**
     * List of structure for this model.
     */
    public function structure($structure): array
    {
        $structure['table'] = ['title', 'slug', 'value', 'ledger_id', 'method', 'published'];
        $structure['form'] = [
            ['label' => 'Title', 'class' => 'col-span-full', 'fields' => ['title']],
            ['label' => 'Rate', 'class' => 'col-span-full md:col-span-6', 'fields' => ['slug', 'value', 'ledger_id']],
            ['label' => 'Setting', 'class' => 'col-span-full md:col-span-6', 'fields' => ['method', 'published']],
        ];
        $structure['filter'] = ['title', 'value', 'ledger_id', 'published'];

        return $structure;
    }

}
