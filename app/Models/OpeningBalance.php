<?php
namespace Modules\Account\Models;

use Base\Casts\Money;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Modules\Account\Models\ChartOfAccount;
use Modules\Account\Models\FinancialYear;
use Modules\Account\Models\Ledger;
use Modules\Base\Models\BaseModel;

class OpeningBalance extends BaseModel
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
    protected $fillable = ['financial_year_id', 'chart_id', 'ledger_id', 'type', 'debit', 'credit'];

    /**
     * The fields that can be filled
     *
     * @var string
     */
    protected $table = "account_opening_balance";

    /**
     * Add relationship to Ledger
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ledger(): BelongsTo
    {
        return $this->belongsTo(Ledger::class);
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
     * Add relationship to FinancialYear
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function financialYear(): BelongsTo
    {
        return $this->belongsTo(FinancialYear::class);
    }

    public function migration(Blueprint $table): void
    {

        $table->foreignId('financial_year_id')->nullable()->constrained(table: 'account_financial_year')->onDelete('set null');
        $table->foreignId('chart_id')->nullable()->constrained(table: 'account_chart_of_account')->onDelete('set null');
        $table->foreignId('ledger_id')->nullable()->constrained(table: 'account_ledger')->onDelete('set null');
        $table->string('type', 50)->nullable();
        $table->integer('debit')->default(0);
        $table->integer('credit')->default(0);
        $table->string('currency')->default('USD');

    }
}
