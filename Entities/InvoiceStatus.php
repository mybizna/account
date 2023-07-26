<?php

namespace Modules\Account\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Schema\Blueprint;

use Modules\Core\Classes\Views\ListTable;
use Modules\Core\Classes\Views\FormBuilder;

class InvoiceStatus extends BaseModel
{

    protected $fillable = ['type_name', 'slug'];
    public $migrationDependancy = [];
    protected $table = "account_invoice_status";


    public function listTable(){
        // listing view fields
        $fields = new ListTable();

        $fields->name('type_name')->type('text')->ordering(true);
        $fields->name('slug')->type('text')->ordering(true);

        return $fields;

    }
    
    public function formBuilder(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('type_name')->type('text')->group('w-1/2');
        $fields->name('slug')->type('text')->group('w-1/2');

        return $fields;

    }

    public function filter(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('type_name')->type('text')->group('w-1/6');
        $fields->name('slug')->type('text')->group('w-1/6');
        

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
        $table->string('type_name')->nullable();
        $table->string('slug')->nullable();

    }

}
