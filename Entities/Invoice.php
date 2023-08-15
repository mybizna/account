<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class Invoice extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'invoice_no', 'partner_id', 'due_date', 'module', 'model', 'status',
        'description', 'is_posted', 'total',
    ];

    /**
     * The fields that are to be render when performing relationship queries.
     *
     * @var array<string>
     */
    public $rec_names = ['title'];

    /**
     * List of tables names that are need in this model during migration.
     *
     * @var array<string>
     */
    public array $migrationDependancy = ['partner'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice";

    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     * @return void
     */
    public function fields(Blueprint $table): void
    {
        $this->fields = $table ?? new Blueprint($this->table);

        $this->fields->increments('id')->html('text');
        $this->fields->string('title')->html('text');
        $this->fields->char('invoice_no', 100)->html('text');
        $this->fields->foreignId('partner_id')->html('recordpicker')->table(['partner']);
        $this->fields->date('due_date')->useCurrent()->html('datetime');
        $this->fields->string('module')->default('Account')->html('text');
        $this->fields->string('model')->default('Invoice')->html('text');
        $this->fields->enum('status', ['draft', 'pending', 'partial', 'paid', 'closed', 'void'])->html('switch')->default('draft')->nullable();
        $this->fields->string('description')->nullable()->html('textarea');
        $this->fields->tinyInteger('is_posted')->default(0)->html('switch');
        $this->fields->decimal('total', 20, 2)->nullable()->html('amount');
    }

}
