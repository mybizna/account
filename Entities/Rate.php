<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class Rate extends BaseModel
{

    protected $fillable = [
        'title', 'slug', 'value', 'ledger_id',
        'value', 'method', 'params', 'ordering',
        'on_total', 'published',
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
        $table->foreignId('ledger_id');
        $table->decimal('value', 20, 2);
        $table->enum('method', ['+', '+%', '-', '-%'])->default('+');
        $table->string('params')->nullable();
        $table->tinyInteger('ordering')->nullable();
        $table->tinyInteger('on_total')->default(false);
        $table->tinyInteger('published')->default(false);
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_ledger', 'ledger_id');
    }
}
