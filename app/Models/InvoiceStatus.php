<?php
namespace Modules\Account\Models;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Models\BaseModel;

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

        $table->string('name');
        $table->string('slug')->nullable();
        $table->string('color');
        $table->string('status');
        $table->string('description')->nullable();

    }
}
