<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;

use Modules\Core\Classes\Views\ListTable;
use Modules\Core\Classes\Views\FormBuilder;

class FinancialYear extends BaseModel
{

    protected $fillable = ['name', 'start_date', 'end_date', 'description'];
    public $migrationDependancy = [];
    protected $table = "account_financial_year";

    protected $can_delete = "false";


    public function listTable(){
        // listing view fields
        $fields = new ListTable();

        $fields->name('name')->type('text')->ordering(true);
        $fields->name('start_date')->type('datetime')->ordering(true);
        $fields->name('end_date')->type('datetime')->ordering(true);

        return $fields;

    }
    
    public function formBuilder(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('name')->type('text')->group('w-1/2');
        $fields->name('start_date')->type('datetime')->group('w-1/2');
        $fields->name('end_date')->type('datetime')->group('w-1/2');
        $fields->name('description')->type('textarea')->group('w-full');

        return $fields;

    }

    public function filter(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('name')->type('text')->group('w-1/2');
        $fields->name('start_date')->type('datetime')->group('w-1/2');
        $fields->name('end_date')->type('datetime')->group('w-1/2');

        return $fields;

    }
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
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->string('description')->nullable();
    }
}
