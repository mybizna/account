<?php
namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Modules\Account\Models\Rate;
use Modules\Base\Models\BaseModel;
use Modules\Core\Models\Country;

class RateDisallowedin extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['country_id', 'rate_id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_rate_disallowedin";

    /**
     * Add relationship to Rate
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class);
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
        $table->unsignedBigInteger('rate_id')->nullable();

    }

    public function post_migration(Blueprint $table): void
    {
        $table->foreign('country_id')->references('id')->on('core_country')->onDelete('set null');
        $table->foreign('rate_id')->references('id')->on('account_rate')->onDelete('set null');
    }

}
