<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class InvoiceItem extends BaseModel
{

    protected $fillable = [
        'title', 'invoice_id', 'ledger_id', 'price', 'amount', 'quantity',
        'module', 'model', 'item_id',
    ];
    public $migrationDependancy = ['account_invoice', 'account_transaction'];
    protected $table = "account_invoice_item";

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
        $table->integer('invoice_id');
        $table->integer('ledger_id');
        $table->decimal('price', 20, 2)->default(0.00);
        $table->decimal('amount', 20, 2)->default(0.00);
        $table->string('module')->nullable();
        $table->string('model')->nullable();
        $table->integer('item_id')->nullable();
        $table->integer('quantity')->nullable();
    }

    public function post_migration(Blueprint $table)
    {
        if (Migration::checkKeyExist('account_invoice_item', 'invoice_id')) {
            $table->foreign('invoice_id')->references('id')->on('account_invoice')->nullOnDelete();
        }

        if (Migration::checkKeyExist('account_invoice_item', 'transaction_id')) {
            $table->foreign('transaction_id')->references('id')->on('account_transaction')->nullOnDelete();
        }

        if (Migration::checkKeyExist('account_invoice_item', 'ledger_id')) {
            $table->foreign('ledger_id')->references('id')->on('account_ledger')->nullOnDelete();
        }
    }
}
