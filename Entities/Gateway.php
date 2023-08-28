<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

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
     * The fields that are to be render when performing relationship queries.
     *
     * @var array<string>
     */
    public $rec_names = ['title'];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = ['core_currency', 'account_ledger'];

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
    protected $can_delete = false;

    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     *
     * @return void
     */
    public function fields(Blueprint $table = null): void
    {
        $this->fields = $table ?? new Blueprint($this->table);

        $this->fields->increments('id')->html('hidden');
        $this->fields->string('title')->html('text');
        $this->fields->string('slug')->html('text');
        $this->fields->foreignId('ledger_id')->html('recordpicker')->relation(['account', 'ledger']);
        $this->fields->foreignId('currency_id')->nullable()->html('recordpicker')->relation(['core', 'currency']);
        $this->fields->string('image')->nullable()->html('text');
        $this->fields->string('url')->nullable()->html('text');
        $this->fields->string('module')->nullable()->html('text');
        $this->fields->string('instruction')->nullable()->html('textarea');
        $this->fields->integer('ordering')->nullable()->html('text');
        $this->fields->tinyInteger('is_default')->default(0)->html('switch');
        $this->fields->tinyInteger('is_hidden')->default(0)->html('switch');
        $this->fields->tinyInteger('is_hide_in_invoice')->default(1)->html('switch');
        $this->fields->tinyInteger('published')->default(0)->html('switch');
    }

    /**
     * List of structure for this model.
     */
    public function structure($structure): array
    {
        $structure['table'] = ['title', 'slug', 'ledger_id', 'currency_id', 'image', 'ordering', 'published'];
        $structure['form'] = [
            ['label' => 'Title', 'class' => 'col-span-full', 'fields' => ['title']],
            ['label' => 'Gateway', 'class' => 'col-span-6', 'fields' => ['slug', 'ledger_id', 'currency_id']],
            ['label' => 'Image & Setting', 'class' => 'col-span-6', 'fields' => ['image', 'ordering', 'published']],
        ];
        $structure['filter'] = ['title', 'slug', 'ledger_id', 'currency_id', 'published'];

        return $structure;
    }

}
