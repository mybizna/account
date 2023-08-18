<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class RateFile extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'rate_id', 'year', 'month', 'token', 'type', 'max_limit', 'file', 'is_processed',
    ];
    /**
     * The fields that are to be render when performing relationship queries.
     *
     * @var array<string>
     */
    public $rec_names = ['rate_id', 'year', 'month'];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = ['account_rate'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_rate_file";

    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     * @return void
     */
    public function fields(Blueprint $table = null): void
    {
        $this->fields = $table ?? new Blueprint($this->table);

        $this->fields->increments('id')->html('text');
        $this->fields->foreignId('rate_id')->html('recordpicker')->relation(['account', 'rate']);
        $this->fields->string('year')->html('text');
        $this->fields->string('month')->html('text');
        $this->fields->string('token')->html('text');
        $this->fields->string('type')->html('text');
        $this->fields->integer('max_limit')->html('text');
        $this->fields->string('file')->html('text');
        $this->fields->tinyInteger('is_processed')->nullable()->html('switch');
    }

    /**
     * List of structure for this model.
     */
    public function structure($structure): array
    {
        $structure['table'] = ['rate_id', 'year', 'month', 'token', 'type', 'max_limit', 'is_processed'];
        $structure['form'] = [
            ['label' => 'Title', 'class' => 'col-span-full', 'fields' => ['title']],
            ['label' => 'Rate File', 'class' => 'col-span-6', 'fields' => ['rate_id', 'year', 'month', 'token']],
            ['label' => 'Setting', 'class' => 'col-span-6', 'fields' => ['type', 'max_limit', 'is_processed']],
        ];
        $structure['filter'] = ['rate_id', 'year', 'month', 'is_processed'];

        return $structure;
    }

}
