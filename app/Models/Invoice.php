<?php

namespace Modules\Account\Models;

use Modules\Account\Models\InvoiceCoupon;
use Modules\Account\Models\InvoiceItem;
use Modules\Base\Models\BaseModel;
use Modules\Partner\Models\Partner;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;

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
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_invoice";

    /**
     * Add relationship to Partner
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Add relationship to InvoiceItem
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Add relationship to InvoiceCoupon
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(InvoiceCoupon::class);
    }

    public function migration(Blueprint $table): void
    {
        $table->string('title');
        $table->char('invoice_no', 100);
        $table->foreignId('partner_id')->nullable()->constrained(table: 'partner_partner');
        $table->date('due_date');
        $table->string('module')->default('Account');
        $table->string('model')->default('Invoice');
        $table->enum('status', ['draft', 'pending', 'partial', 'paid', 'closed', 'void'])->default('draft');
        $table->string('description')->nullable();
        $table->tinyInteger('is_posted')->default(0);
        $table->decimal('total', 20, 2)->nullable();
    }
}
