<?php
namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Modules\Account\Models\Gateway;
use Modules\Base\Models\BaseModel;
use Modules\Core\Models\Country;

class GatewayAllowedin extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['country_id', 'gateway_id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_gateway_allowedin";

    /**
     * Add relationship to Gateway
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
    }

    /**
     * Add relationship to Country
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
    public function migration(Blueprint $table): void
    {
        $table->unsignedBigInteger('country_id')->nullable();
        $table->unsignedBigInteger('gateway_id')->nullable();
    }

    public function post_migration(Blueprint $table): void
    {
        $table->foreign('country_id')->references('id')->on('core_country')->onDelete('set null');
        $table->foreign('gateway_id')->references('id')->on('account_gateway')->onDelete('set null');
    }
}
