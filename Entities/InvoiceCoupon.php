<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class InvoiceCoupon extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['payment_id', 'coupon_id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice_coupon";

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
        $this->fields->foreignId('payment_id')->html('recordpicker')->relation(['account', 'payment']);
        $this->fields->foreignId('coupon_id')->html('recordpicker')->relation(['account', 'coupon']);
    }



}
