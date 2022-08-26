<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

class InvoiceItemRate extends BaseModel
{

    protected $fillable = [
        'title', 'slug', 'rate_id', 'invoice_item_id', 'method', 'value', 'params', 'ordering', 'on_total',
    ];
    public $migrationDependancy = ['account_invoice_item', 'account_rate'];
    protected $table = "account_invoice_item_rate";

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $table->increments('id');
        $table->integer('title');
        $table->integer('slug');
        $table->integer('rate_id');
        $table->integer('invoice_item_id');
        $table->enum('method', ['+', '+%', '-', '-%'])->default('+')->nullable();
        $table->decimal('value', 20, 2)->default(0.00);
        $table->string('params')->nullable();
        $table->tinyInteger('ordering')->nullable();
        $table->tinyInteger('on_total')->nullable();
    }

    public function post_migration(Blueprint $table)
    {
        if (Migration::checkKeyExist('account_invoice_item_rate', 'invoice_id')) {
            $table->foreign('invoice_id')->references('id')->on('account_invoice')->nullOnDelete();
        }

        if (Migration::checkKeyExist('account_invoice_item_rate', 'rate_id')) {
            $table->foreign('rate_id')->references('id')->on('account_transaction')->nullOnDelete();
        }
    }
}
