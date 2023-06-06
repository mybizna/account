<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

class InvoiceCoupon extends BaseModel
{

    protected $fillable = ['payment_id', 'coupon_id'];
    public $migrationDependancy = ['account_payment', 'account_coupon'];
    protected $table = "account_invoice_coupon";

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $table->increments('id');
        $table->foreignId('payment_id');
        $table->foreignId('coupon_id');
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_coupon', 'coupon_id');
        Migration::addForeign($table, 'account_payment', 'payment_id');
    }
}
