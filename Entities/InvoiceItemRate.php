<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class InvoiceItemRate extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'slug', 'rate_id', 'invoice_item_id', 'method', 'value', 'params', 'ordering', 'on_total',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice_item_rate";

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
        $this->fields->integer('title')->html('text');
        $this->fields->integer('slug')->html('text');
        $this->fields->foreignId('rate_id')->html('recordpicker')->relation(['account', 'rate']);
        $this->fields->foreignId('invoice_item_id')->html('recordpicker')->relation(['account', 'invoice_item']);
        $this->fields->enum('method', array_keys($methods))->default('+')->html('select')->options($methods);
        $this->fields->decimal('value', 20, 2)->default(0.00)->html('amount');
        $this->fields->string('params')->nullable()->html('text');
        $this->fields->tinyInteger('ordering')->nullable()->html('text');
        $this->fields->tinyInteger('on_total')->default(false)->html('switch');
    }


    

}
