<?php

namespace Modules\Account\Models;

use Modules\Account\Models\InvoiceItem;
use Modules\Account\Models\Rate;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Base\Models\BaseModel;
use Base\Casts\Money;

class InvoiceItemRate extends BaseModel
{

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total' => Money::class, // Use the custom MoneyCast
    ];

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'slug', 'rate_id', 'invoice_item_id', 'method', 'value', 'params', 'ordering', 'on_total',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice_item_rate";

    /**
     * Add relationship to InvoiceItem
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoiceItem(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    /**
     * Add relationship to Rate
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class);
    }

    public function migration(Blueprint $table): void
    {

        $table->string('title');
        $table->string('slug');
        $table->unsignedBigInteger('rate_id')->nullable();
        $table->unsignedBigInteger('invoice_item_id')->nullable();
        $table->enum('method', ['+', '+%', '-', '-%'])->default('+');
        $table->integer('value')->default(0);
        $table->string('currency')->default('USD');
        $table->string('params')->nullable();
        $table->tinyInteger('ordering')->nullable();
        $table->tinyInteger('on_total')->default(false);

    }

    public function post_migration(Blueprint $table): void
    {
        $table->foreign('rate_id')->references('id')->on('account_rate')->onDelete('set null');
        $table->foreign('invoice_item_id')->references('id')->on('account_invoice_item')->onDelete('set null');
    }

}
