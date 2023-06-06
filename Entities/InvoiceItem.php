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
        $table->foreignId('invoice_id');
        $table->foreignId('ledger_id');
        $table->decimal('price', 20, 2)->default(0.00);
        $table->decimal('amount', 20, 2)->default(0.00);
        $table->string('module')->nullable();
        $table->string('model')->nullable();
        $table->foreignId('item_id')->nullable();
        $table->integer('quantity')->nullable();
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_invoice', 'invoice_id');
        Migration::addForeign($table, 'account_transaction', 'transaction_id');
        Migration::addForeign($table, 'account_ledger', 'ledger_id');
    }
}
