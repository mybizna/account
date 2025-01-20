<?php
namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Modules\Account\Models\Ledger;
use Modules\Base\Models\BaseModel;
use Modules\Core\Models\Currency;

class Gateway extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'slug', 'ledger_id', 'currency_id', 'image', 'url', 'instruction',
        'module', 'ordering', 'is_default', 'is_hidden', 'is_hide_in_invoice', 'published',

    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_gateway";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected bool $can_delete = false;

    /**
     * Add relationship to Ledger
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ledger(): BelongsTo
    {
        return $this->belongsTo(Ledger::class);
    }

    /**
     * Add relationship to Currency
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Add relationship to GatewayRate
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rates(): HasMany
    {
        return $this->hasMany(GatewayRate::class);
    }

    /**
     * Add relationship to GatewayAllowedin
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allowedins(): HasMany
    {
        return $this->hasMany(GatewayAllowedin::class);
    }

    /**
     * Add relationship to GatewayDisallowedin
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function disallowedins()
    {
        return $this->hasMany(GatewayDisallowedin::class);
    }

    public function migration(Blueprint $table): void
    {
        $table->string('title');
        $table->string('slug');
        $table->unsignedBigInteger('ledger_id')->nullable();
        $table->unsignedBigInteger('currency_id')->nullable();
        $table->string('image')->nullable();
        $table->string('url')->nullable();
        $table->string('module')->nullable();
        $table->string('instruction')->nullable();
        $table->integer('ordering')->nullable();
        $table->tinyInteger('is_default')->default(0);
        $table->tinyInteger('is_hidden')->default(0);
        $table->tinyInteger('is_hide_in_invoice')->default(1);
        $table->tinyInteger('published')->default(0);

    }

    public function post_migration(Blueprint $table): void
    {
        $table->foreign('ledger_id')->nullable()->constrained(table: 'account_ledger')->onDelete('set null');
        $table->foreign('currency_id')->nullable()->constrained(table: 'core_currency')->onDelete('set null');
    }
}
