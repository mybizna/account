<?php

namespace Modules\Account\Entities;

use Modules\Core\Entities\BaseModel AS Model;
use Illuminate\Database\Schema\Blueprint;

class CurrencyInfo extends Model
{

    protected $fillable = ['name', 'sign'];
    public $migrationDependancy = [];
    protected $table = "account_currency_info";

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $table->increments('id');
        $table->string('name')->nullable();
        $table->string('sign')->nullable();
    }
}
