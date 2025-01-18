<?php

namespace Modules\Account\Models;

use Modules\Account\Models\Rate;
use Modules\Base\Models\BaseModel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

        $table->foreignId('rate_id')->nullable()->constrained(table: 'account_rate')->onDelete('set null');
        $table->string('year')->nullable();
        $table->string('month')->nullable();
        $table->string('token')->nullable();
        $table->string('type')->nullable();
        $table->integer('max_limit')->nullable();
        $table->string('file')->nullable();
        $table->tinyInteger('is_processed')->nullable();

    }

}
