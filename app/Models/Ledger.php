<?php

namespace Modules\Account\Models;

use Modules\Account\Models\ChartOfAccount;
use Modules\Account\Models\LedgerCategory;
use Modules\Base\Models\BaseModel;

class Ledger extends BaseModel
{

    /**
     * The fields that can be filled
     *
     * @var array<string>
     */
    protected $fillable = ['chart_id', 'category_id', 'name', 'slug', 'code', 'unused', 'is_system'];

    /**
     * The fields that can be filled
     *
     * @var string
     */
    protected $table = "account_ledger";

    /**
     * This model is not deletable.
     *
     * @var bool
     */
    protected bool $can_delete = false;

    /**
     * Add relationship to Chart
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chart()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    /**
     * Add relationship to Category
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(LedgerCategory::class);
    }

    /**
     * Function for deleting a record.
     *
     * @param int $id
     *
     * @return array
     */
    public function deleteRecord($id)
    {

        $ledger = $this->where('id', $id)->first();

        if ($ledger->is_system) {
            return [
                'module' => $this->module,
                'model' => $this->model,
                'status' => 0,
                'error' => 1,
                'record' => [],
                'message' => 'You can not Delete a Lerger Set by system.',
            ];
        }

        return parent::deleteRecord($id);

    }
}
