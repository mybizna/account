<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class InvoiceItem extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'invoice_id', 'ledger_id', 'price', 'amount', 'quantity',
        'module', 'model', 'item_id',
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
    public array $migrationDependancy = ['account_invoice', 'account_ledger'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice_item";

    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     * @return void
     */
    public function fields(Blueprint $table = null): void
    {

        $this->fields = $table ?? new Blueprint($this->table);

        $this->fields->increments('id')->html('hidden');
        $this->fields->string('title')->html('text');
        $this->fields->foreignId('invoice_id')->html('recordpicker')->relation(['account', 'invoice']);
        $this->fields->foreignId('ledger_id')->html('recordpicker')->relation(['account', 'ledger']);
        $this->fields->decimal('price', 20, 2)->default(0.00)->html('amount');
        $this->fields->decimal('amount', 20, 2)->default(0.00)->html('amount');
        $this->fields->string('module')->nullable()->html('text');
        $this->fields->string('model')->nullable()->html('text');
        $this->fields->foreignId('item_id')->nullable()->html('text');
        $this->fields->integer('quantity')->nullable()->html('number');
    }

    /**
     * List of structure for this model.
     */
    public function structure($structure): array
    {
        $structure['table'] = ['title', 'invoice_id', 'ledger_id', 'price', 'amount', 'quantity'];
        $structure['form'] = [
            ['label' => 'Invoice Item Title', 'class' => 'col-span-full', 'fields' => ['title']],
            ['label' => 'Invoice Item Detail', 'class' => 'col-span-full  md:col-span-6 md:pr-2', 'fields' => ['invoice_id', 'ledger_id', 'quantity']],
            ['label' => 'Invoice Item Amount', 'class' => 'col-span-full  md:col-span-6 md:pr-2', 'fields' => ['price', 'amount']],
        ];
        $structure['filter'] = ['title', 'invoice_id', 'ledger_id'];

        return $structure;
    }

    /**
     * Define rights for this model.
     *
     * @return array
     */
    public function rights(): array
    {
        $rights = parent::rights();

        $rights['staff'] = ['view' => true, 'add' => true];
        $rights['registered'] = ['view' => true, 'add' => true];
        $rights['guest'] = [];

        return $rights;
    }

}
