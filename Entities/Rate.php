<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;


class Rate extends BaseModel
{

    protected $fillable = [
        'title', 'slug', 'value', 'start_amount', 'end_amount', 'ledger_id',
        'file_limit', 'file_type', 'file_structure', 'file_suffix',
        'is_percent', 'is_visible', 'published'
    ];
    public $migrationDependancy = ['account_ledger'];
    protected $table = "account_rate";

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
        $table->string('slug');
        $table->decimal('value', 20, 2)->default(0.00);
        $table->decimal('start_amount', 20, 2)->default(0.00);
        $table->decimal('end_amount', 20, 2)->default(0.00);
        $table->integer('ledger_id')->nullable();

        $table->integer('file_limit')->nullable();
        $table->string('file_type')->nullable();
        $table->string('file_structure')->nullable();
        $table->string('file_suffix')->nullable();

        $table->tinyInteger('is_percent')->nullable();
        $table->tinyInteger('is_visible')->nullable();
        $table->tinyInteger('published')->nullable();
    }

    public function post_migration(Blueprint $table)
    {
        if (Migration::checkKeyExist('account_rate', 'ledger_id')) {
            $table->foreign('ledger_id')->references('id')->on('account_ledger')->nullOnDelete();
        }
    }
}
