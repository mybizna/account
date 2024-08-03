<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

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
     * List of fields to be migrated to the datebase when creating or updating model during migration.
     *
     * @param Blueprint $table
     * @return void
     */
    public function fields(Blueprint $table = null): void
    {

        $this->fields = $table ?? new Blueprint($this->table);

        $this->fields->increments('id')->html('hidden');
        $this->fields->foreignId('chart_id')->nullable()->html('recordpicker')->relation(['account', 'chart_of_account']);
        $this->fields->foreignId('category_id')->nullable()->html('recordpicker')->relation(['account', 'ledger_category']);
        $this->fields->string('name')->nullable()->html('text');
        $this->fields->string('slug')->nullable()->html('text');
        $this->fields->integer('code')->nullable()->html('text');
        $this->fields->tinyInteger('unused')->default(1)->html('switch');
        $this->fields->tinyInteger('is_system')->default(0)->html('switch');
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
