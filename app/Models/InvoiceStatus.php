<?php

namespace Modules\Account\Models;

use Modules\Base\Models\BaseModel;
use Illuminate\Database\Schema\Blueprint;

class InvoiceStatus extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'slug', 'color', 'status'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice_status";

    public function migration(Blueprint $table): void
    {
        $table->id();

        $table->string('name');
        $table->string('slug')->nullable();
        $table->string('color');
        $table->string('status');
        $table->string('description')->nullable();

    }
}
