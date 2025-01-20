<?php
namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Modules\Account\Models\Rate;
use Modules\Base\Models\BaseModel;

class RateFile extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'rate_id', 'year', 'month', 'token', 'type', 'max_limit', 'file', 'is_processed',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "account_rate_file";

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
        $table->unsignedBigInteger('rate_id')->nullable();
        $table->string('year')->nullable();
        $table->string('month')->nullable();
        $table->string('token')->nullable();
        $table->string('type')->nullable();
        $table->integer('max_limit')->nullable();
        $table->string('file')->nullable();
        $table->tinyInteger('is_processed')->nullable();

    }

    public function post_migration(Blueprint $table): void
    {
        $table->foreign('rate_id')->references('id')->on('account_rate')->onDelete('set null');
    }

}
