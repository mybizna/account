<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Entities\BaseModel;

class Payment extends BaseModel
{
    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'amount', 'ledger_id', 'partner_id', 'gateway_id', 'receipt_no',
        'code', 'others', 'stage', 'status', "type", 'is_posted',
    ];

    /**
     * The fields that can be filled
     *
     * @var string
     */
    protected $table = "account_payment";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected bool $can_delete = false;

    /**
     * Set if model is visible from frontend.
     *
     * @var bool
     */
    public bool $show_frontend = true;

    /**
     * List of field for this model.
     * @param Blueprint $table
     */
    public function fields(Blueprint $table = null): void
    {
        $this->fields = $table ?? new Blueprint($this->table);

        $stage = ['pending' => 'Pending', 'wallet' => 'Wallet', 'posted' => 'Posted'];
        $status = ['pending' => 'Pending', 'paid' => 'Paid', 'reversed' => 'Reversed', 'canceled' => 'Canceled'];

        $stage_colors = ['pending' => 'red', 'wallet' => 'green', 'posted' => 'blue'];
        $status_colors = ['pending' => 'red', 'paid' => 'green', 'reversed' => 'blue', 'canceled' => 'gray'];

        $this->fields->increments('id')->html('hidden');
        $this->fields->string('title')->html('text');
        $this->fields->decimal('amount', 20, 2)->html('amount');
        $this->fields->foreignId('ledger_id')->html('recordpicker')->relation(['account', 'ledger']);
        $this->fields->foreignId('partner_id')->html('recordpicker')->relation(['partner']);
        $this->fields->foreignId('gateway_id')->html('recordpicker')->relation(['account', 'gateway']);
        $this->fields->string('receipt_no')->nullable();
        $this->fields->string('code')->html('text')->nullable();
        $this->fields->string('others')->html('text')->nullable();
        $this->fields->enum('stage', array_keys($stage))->html('select')->color($stage_colors)->options($stage)->default('pending');
        $this->fields->enum('status', array_keys($status))->html('select')->color($status_colors)->options($status)->default('pending');
        $this->fields->enum('type', ['in', 'out'])->html('select')->default('in');
        $this->fields->tinyInteger('is_posted')->html('switch')->default(false);
    }

 



}
