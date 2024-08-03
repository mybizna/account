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

        $this->fields->increments('id')->html('hidden');
        $this->fields->foreignId('rate_id')->html('recordpicker')->relation(['account', 'rate']);
        $this->fields->string('year')->html('text');
        $this->fields->string('month')->html('text');
        $this->fields->string('token')->html('text');
        $this->fields->string('type')->html('text');
        $this->fields->integer('max_limit')->html('text');
        $this->fields->string('file')->html('text');
        $this->fields->tinyInteger('is_processed')->nullable()->html('switch');
    }





}
