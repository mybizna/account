<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;

class InvoiceItemRate extends BaseModel
{

    protected $fillable = [
        'invoice_item_id', 'rate_id','title', 'slug', 'value','is_percent',
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
        $table->integer('invoice_item_id');
        $table->integer('rate_id');
        $table->integer('title')->nullable();
        $table->integer('slug')->nullable();
        $table->decimal('value', 20, 2)->default(0.00);
        $table->tinyInteger('is_percent')->nullable();

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