<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

class PaymentInvoice extends BaseModel
{

    protected $fillable = ['payment_id', 'invoice_id'];
    public $migrationDependancy = ['account_payment', 'account_invoice'];
    protected $table = "account_payment_invoice";

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $table->increments('id');
        $table->integer('payment_id');
        $table->integer('invoice_id');
    }

    public function post_migration(Blueprint $table)
    {
        if (Migration::checkKeyExist('account_payment_rate', 'payment_id')) {
            $table->foreign('payment_id')->references('id')->on('account_payment')->nullOnDelete();
        }

        if (Migration::checkKeyExist('account_payment_invoice', 'invoice_id')) {
            $table->foreign('invoice_id')->references('id')->on('account_invoice')->nullOnDelete();
        }
    }
}
