<?php
namespace Modules\Account\Models;

use Base\Casts\Money;
use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Models\BaseModel;

class Coupon extends BaseModel
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
        'code', 'description', 'value', 'start_amount', 'end_amount',
        'start_date', 'end_date', 'applied', 'is_percent', 'published', 'is_visible',
    ];

    /**
     * The this->fields associated with the model.
     *
     * @var string
     */
    protected $table = "account_coupon";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected bool $can_delete = false;

    public function migration(Blueprint $table): void
    {
        $table->string('code')->nullable();
        $table->string('description')->nullable();
        $table->string('value')->nullable();
        $table->integer('start_amount')->default(0);
        $table->integer('end_amount')->default(0);
        $table->string('currency')->default('USD');
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->string('applied')->nullable();
        $table->tinyInteger('is_percent')->default(0);
        $table->tinyInteger('published')->default(0);
        $table->tinyInteger('is_visible')->default(0);
    }
}
