<?php
namespace Modules\Account\Models;

use Base\Casts\Money;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Modules\Account\Models\ChartOfAccount;
use Modules\Account\Models\Ledger;
use Modules\Base\Models\BaseModel;
use Modules\Partner\Models\Partner;

class Transaction extends BaseModel
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

        $table->integer('amount')->default(0);
        $table->string('currency')->default('USD');
        $table->string('description');
        $table->unsignedBigInteger('partner_id')->nullable();
        $table->unsignedBigInteger('left_chart_of_account_id')->nullable();
        $table->unsignedBigInteger('left_ledger_id')->nullable();
        $table->unsignedBigInteger('right_chart_of_account_id')->nullable();
        $table->unsignedBigInteger('right_ledger_id')->nullable();
        $table->tinyInteger('is_processed')->nullable();

    }
    public function post_migration(Blueprint $table): void
    {
        $table->foreign('partner_id')->references('id')->on('partner_partner')->onDelete('set null');
        $table->foreign('left_chart_of_account_id')->references('id')->on('account_chart_of_account')->onDelete('set null');
        $table->foreign('left_ledger_id')->references('id')->on('account_ledger')->onDelete('set null');
        $table->foreign('right_chart_of_account_id')->references('id')->on('account_chart_of_account')->onDelete('set null');
        $table->foreign('right_ledger_id')->references('id')->on('account_ledger')->onDelete('set null');
    }

}
