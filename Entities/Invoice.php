<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

use Modules\Core\Classes\Views\ListTable;
use Modules\Core\Classes\Views\FormBuilder;

class Invoice extends BaseModel
{

    protected $fillable = [
        'title', 'invoice_no', 'partner_id', 'due_date', 'module', 'model', 'status', 
        'description', 'is_posted', 'total',
    ];
    public $migrationDependancy = ['partner'];
    protected $table = "account_invoice";


    public function listTable(){
        // listing view fields
        $fields = new ListTable();

        $fields->name('title')->type('text')->ordering(true);
        $fields->name('invoice_no')->type('text')->ordering(true);
        $fields->name('partner_id')->type('recordpicker')->table('partner')->ordering(true);
        $fields->name('due_date')->type('datetime')->ordering(true);
        $fields->name('module')->type('text')->ordering(true);
        $fields->name('model')->type('text')->ordering(true);
        $fields->name('status')->type('switch')->ordering(true);
        $fields->name('description')->type('textarea')->ordering(true);
        $fields->name('is_posted')->type('switch')->ordering(true);


        return $fields;

    }
    
    public function formBuilder(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('title')->type('text')->group('w-1/2');
        $fields->name('invoice_no')->type('text')->group('w-1/2');
        $fields->name('partner_id')->type('recordpicker')->table('partner')->group('w-1/2');
        $fields->name('due_date')->type('datetime')->group('w-1/2');
        $fields->name('module')->type('text')->group('w-1/2');
        $fields->name('model')->type('text')->group('w-1/2');
        $fields->name('status')->type('switch')->group('w-1/2');
        $fields->name('description')->type('textarea')->group('w-full');
        $fields->name('is_posted')->type('switch')->group('w-1/2');

        return $fields;

    }

    public function filter(){
        // listing view fields
        $fields = new FormBuilder();

        $fields->name('title')->type('text')->group('w-1/6');
        $fields->name('invoice_no')->type('text')->group('w-1/6');
        $fields->name('partner_id')->type('recordpicker')->table('partner')->group('w-1/6');
        $fields->name('due_date')->type('datetime')->group('w-1/6');
        $fields->name('module')->type('text')->group('w-1/6');
        $fields->name('model')->type('text')->group('w-1/6');
        $fields->name('status')->type('switch')->group('w-1/6');
        $fields->name('is_posted')->type('switch')->group('w-1/6');

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
        $table->string('title');
        $table->char('invoice_no', 100);
        $table->foreignId('partner_id');
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
        Migration::addForeign($table, 'partner', 'partner_id');
    }
}
