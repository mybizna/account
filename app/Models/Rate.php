<?php

namespace Modules\Account\Models;

use Modules\Account\Models\Ledger;
use Modules\Account\Models\RateAllowedin;
use Modules\Account\Models\RateDisallowedin;
use Modules\Account\Models\RateFile;
use Modules\Base\Models\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rate extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'slug', 'value', 'ledger_id',
        'value', 'method', 'params', 'ordering',
        'on_total', 'published',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_rate";

    /**
     * Add relationship to Ledger
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ledger(): BelongsTo
    {
        return $this->belongsTo(Ledger::class);
    }

    /**
     * Add relationship to RateFile
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(RateFile::class);
    }

    /**
     * Add relationship to RateAllowedin
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allowedins(): HasMany
    {
        return $this->hasMany(RateAllowedin::class);
    }

    /**
     * Add relationship to RateDisallowedin
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function disallowedins(): HasMany
    {
        return $this->hasMany(RateDisallowedin::class);
    }
    public function migration(Blueprint $table): void
    {
        $table->id();

        $table->string('title');
        $table->string('slug');
        $table->foreignId('ledger_id')->nullable()->constrained(table: 'account_ledger')->onDelete('set null');
        $table->decimal('value', 20, 2);
        $table->enum('method', ['+', '+%', '-', '-%'])->default('+');
        $table->string('params')->nullable();
        $table->tinyInteger('ordering')->nullable();
        $table->tinyInteger('on_total')->default(false);
        $table->tinyInteger('published')->default(false);

    }
}
