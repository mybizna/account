<?php
namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Modules\Account\Models\Gateway;
use Modules\Account\Models\Rate;
use Modules\Base\Models\BaseModel;

class GatewayRate extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['gateway_id', 'rate_id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_gateway_rate";

    /**
     * Add relationship to Gateway
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
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

        $table->unsignedBigInteger('gateway_id')->nullable();
        $table->unsignedBigInteger('rate_id')->nullable();
    }

    public function post_migration(Blueprint $table): void
    {
        $table->foreign('gateway_id')->references('id')->on('account_gateway')->onDelete('set null');
        $table->foreign('rate_id')->references('id')->on('account_rate')->onDelete('set null');
    }
}
