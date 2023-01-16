<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class Gateway extends BaseModel
{

    protected $fillable = [
        'title', 'slug', 'ledger_id', 'currency_id', 'image', 'url',
        'instruction', 'module', 'ordering', 'is_default', 'is_hidden', 'published',

    ];
    public $migrationDependancy = ['core_currency', 'account_ledger'];
    protected $table = "account_gateway";

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
        $table->integer('ledger_id');
        $table->integer('currency_id')->nullable();
        $table->string('image')->nullable();
        $table->string('url')->nullable();
        $table->string('module')->nullable();
        $table->string('instruction')->nullable();
        $table->integer('ordering')->nullable();
        $table->tinyInteger('is_default')->default(false);
        $table->tinyInteger('is_hidden')->default(false);
        $table->tinyInteger('published')->default(false);
    }

    public function post_migration(Blueprint $table)
    {
        if (Migration::checkKeyExist('account_gateway', 'currency_id')) {
            $table->foreign('currency_id')->references('id')->on('core_currency')->nullOnDelete();
        }

        if (Migration::checkKeyExist('account_gateway', 'ledger_id')) {
            $table->foreign('ledger_id')->references('id')->on('account_ledger')->nullOnDelete();
        }
    }
}
