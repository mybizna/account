<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class Invoice extends BaseModel
{

    protected $fillable = [
        'title', 'invoice_no', 'partner_id', 'due_date', 'module', 'model', 'status', 
        'description', 'is_posted', 'total',
    ];
    public $migrationDependancy = ['partner'];
    protected $table = "account_invoice";

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
        $table->char('invoice_no', 100);
        $table->integer('partner_id');
        $table->date('due_date')->useCurrent();
        $table->string('module')->default('Account');
        $table->string('model')->default('Invoice');
        $table->enum('status', ['draft', 'pending', 'partial', 'paid', 'closed', 'void'])->default('draft')->nullable();
        $table->string('description')->nullable();
        $table->tinyInteger('is_posted')->default(false);
        $table->decimal('total', 20, 2)->nullable();
    }

    public function post_migration(Blueprint $table)
    {
        if (Migration::checkKeyExist('account_invoice', 'partner_id')) {
            $table->foreign('partner_id')->references('id')->on('partner')->nullOnDelete();
        }
    }
}
