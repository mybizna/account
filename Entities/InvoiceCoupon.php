<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Classes\Views\FormBuilder;
use Modules\Base\Classes\Views\ListTable;
use Modules\Base\Entities\BaseModel;

class InvoiceCoupon extends BaseModel
{
    /**
     * The fields that can be filled
     * @var array<string>
     */
    protected $fillable = ['payment_id', 'coupon_id'];

    /**
     * List of tables names that are need in this model during migration.
     * @var array<string>
     */
    public array $migrationDependancy = ['account_payment', 'account_coupon'];

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "account_invoice_coupon";

    public function listTable(): ListTable
    {
        // listing view fields
        $fields = new ListTable();

        $fields->name('payment_id')->type('recordpicker')->table('account_payment')->ordering(true);
        $fields->name('coupon_id')->type('recordpicker')->table('account_coupon')->ordering(true);

        return $fields;

    }

    public function formBuilder(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('payment_id')->type('recordpicker')->table('account_payment')->group('w-1/2');
        $fields->name('coupon_id')->type('recordpicker')->table('account_coupon')->group('w-1/2');

        return $fields;

    }

    public function filter(): FormBuilder
    {
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('payment_id')->type('recordpicker')->table('account_payment')->group('w-1/2');
        $fields->name('coupon_id')->type('recordpicker')->table('account_coupon')->group('w-1/2');

        return $fields;

    }
    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
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
