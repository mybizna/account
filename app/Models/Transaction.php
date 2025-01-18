<?php

namespace Modules\Account\Models;

use Modules\Account\Models\ChartOfAccount;
use Modules\Account\Models\Ledger;
use Modules\Account\Models\LedgerSetting;
use Modules\Base\Models\BaseModel;
use Modules\Partner\Models\Partner;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'amount', 'description', 'partner_id', 'ledger_setting_id', 'left_chart_of_account_id', 'left_ledger_id',
        'right_chart_of_account_id', 'right_ledger_id', 'is_processed',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_transaction";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected bool $can_delete = false;

    /**
     * Add Relationship to Partner
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Add Relationship to LedgerSetting
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ledgerSetting(): BelongsTo
    {
        return $this->belongsTo(LedgerSetting::class);
    }

    /**
     * Add Relationship to ChartOfAccount
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function leftChartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'left_chart_of_account_id');
    }

    /**
     * Add Relationship to Ledger
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function leftLedger(): BelongsTo
    {
        return $this->belongsTo(Ledger::class, 'left_ledger_id');
    }

    /**
     * Add Relationship to ChartOfAccount
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rightChartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'right_chart_of_account_id');
    }

    /**
     * Add Relationship to Ledger
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rightLedger(): BelongsTo
    {
        return $this->belongsTo(Ledger::class, 'right_ledger_id');
    }
    public function migration(Blueprint $table): void
    {
        $table->id();

        $table->decimal('amount', 20, 2)->default(0.00);
        $table->string('description');
        $table->foreignId('partner_id')->nullable()->constrained(table: 'partner_partner')->onDelete('set null');
        $table->foreignId('left_chart_of_account_id')->nullable()->constrained('account_chart_of_account')->onDelete('set null');
        $table->foreignId('left_ledger_id')->nullable()->constrained(table: 'account_ledger')->onDelete('set null');
        $table->foreignId('right_chart_of_account_id')->nullable()->constrained('account_chart_of_account')->onDelete('set null');
        $table->foreignId('right_ledger_id')->nullable()->constrained(table: 'account_ledger')->onDelete('set null');
        $table->tinyInteger('is_processed')->nullable();

    }

}
