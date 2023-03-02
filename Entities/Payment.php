<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class Payment extends BaseModel
{

    protected $fillable = [
        'title', 'amount', 'ledger_id', 'partner_id', 'gateway_id', 'receipt_no',
        'code', 'status', "type", 'is_posted',
    ];
    public $migrationDependancy = ['account_gateway', 'account_ledger', 'partner'];
    protected $table = "account_payment";

    protected $can_delete = "false";

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $table->increments('id');
        $table->string('title');
        $table->decimal('amount', 20, 2);
        $table->integer('ledger_id');
        $table->integer('partner_id');
        $table->integer('gateway_id');
        $table->string('receipt_no')->nullable();
        $table->string('code')->nullable();
        $table->enum('status', ['pending', 'paid', 'reversed','canceled'])->default('pending');
        $table->enum('type', ['in', 'out'])->default('in');
        $table->tinyInteger('is_posted')->default(false);
    }

    public function post_migration(Blueprint $table)
    {
        if (Migration::checkKeyExist('account_payment', 'gateway_id')) {
            $table->foreign('gateway_id')->references('id')->on('account_gateway')->nullOnDelete();
        }

        if (Migration::checkKeyExist('account_payment', 'ledger_id')) {
            $table->foreign('ledger_id')->references('id')->on('account_ledger')->nullOnDelete();
        }

        if (Migration::checkKeyExist('account_payment', 'invoice_id')) {
            $table->foreign('invoice_id')->references('id')->on('account_invoice')->nullOnDelete();
        }
    }
}
