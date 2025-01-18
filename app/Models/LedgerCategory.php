<?php
namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Modules\Account\Models\ChartOfAccount;
use Modules\Account\Models\Ledger;
use Modules\Base\Models\BaseModel;

class LedgerCategory extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'slug', 'chart_id', 'parent_id', 'is_system'];

    /**
     * The fields that can be filled
     *
     * @var string
     */
    protected $table = "account_ledger_category";

    /**
     * Add relationship to Ledger
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function ledgers(): HasMany
    {
        return $this->hasMany(Ledger::class);
    }

    /**
     * Add relationship to ChartOfAccount
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function chart(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    /**
     * Add relationship to Parent
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function parent(): BelongsTo
    {
        return $this->belongsTo(LedgerCategory::class, 'parent_id');
    }
    public function migration(Blueprint $table): void
    {

        $table->string('name');
        $table->string('slug');
        $table->foreignId('chart_id')->nullable()->constrained(table: 'account_chart_of_account')->onDelete('set null');
        $table->foreignId('parent_id')->nullable()->constrained(table: 'account_ledger_category')->onDelete('set null');
        $table->tinyInteger('is_system');

    }

}
