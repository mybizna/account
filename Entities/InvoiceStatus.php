<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Entities\BaseModel;

class InvoiceStatus extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['type_name', 'slug'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice_status";

    /**
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     *
     * @return void
     */
    public function fields(Blueprint $table = null): void
    {

        $this->fields = $table ?? new Blueprint($this->table);

        $this->fields->increments('id');
        $this->fields->string('type_name')->nullable()->html('text');
        $this->fields->string('slug')->nullable()->html('text');

    }



}
