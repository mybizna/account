<?php

namespace Modules\Account\Models;

use Modules\Account\Models\Invoice;
use Modules\Account\Models\InvoiceItemRate;
use Modules\Account\Models\Ledger;
use Modules\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;

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
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice_item";

    /**
     * Add relationship for InvoiceItemRate
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rates(): HasMany
    {
        return $this->hasMany(InvoiceItemRate::class);
    }

    /**
     * Add relationship to Invoice
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Add relationship to Ledger
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ledger(): BelongsTo
    {
        return $this->belongsTo(Ledger::class);
    }

    public function migration(Blueprint $table): void
    {

        $table->string('title');
        $table->foreignId('invoice_id')->nullable()->constrained(table: 'account_invoice')->onDelete('set null');
        $table->foreignId('ledger_id')->nullable()->constrained(table: 'account_ledger')->onDelete('set null');
        $table->integer('price')->default(0);
        $table->integer('amount')->default(0);
        $table->string('module')->nullable();
        $table->string('model')->nullable();
        $table->bigInteger('item_id')->nullable();
        $table->integer('quantity')->nullable();

    }

}
