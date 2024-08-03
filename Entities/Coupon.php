<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Entities\BaseModel;

class Coupon extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'code', 'description', 'value', 'start_amount', 'end_amount',
        'start_date', 'end_date', 'applied', 'is_percent', 'published', 'is_visible',
    ];

    /**
     * The this->fields associated with the model.
     *
     * @var string
     */
    protected $table = "account_coupon";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected bool $can_delete = false;


    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $this->fields
     *
     * @return void
     */
    public function fields(Blueprint $table = null): void
    {
        $this->fields = $table ?? new Blueprint($this->table);

        $this->fields->increments('id')->html('hidden');
        $this->fields->string('code')->nullable()->html('text');
        $this->fields->string('description')->nullable()->html('textarea');
        $this->fields->string('value')->nullable()->html('text');
        $this->fields->decimal('start_amount', 20, 2)->default(0.00)->html('amount');
        $this->fields->decimal('end_amount', 20, 2)->default(0.00)->html('amount');
        $this->fields->date('start_date')->nullable()->html('date');
        $this->fields->date('end_date')->nullable()->html('date');
        $this->fields->string('applied')->nullable()->html('switch');
        $this->fields->tinyInteger('is_percent')->default(0)->html('switch');
        $this->fields->tinyInteger('published')->default(0)->html('switch');
        $this->fields->tinyInteger('is_visible')->default(0)->html('switch');

    }


}
