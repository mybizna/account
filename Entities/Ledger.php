<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Schema\Blueprint;
use Modules\Base\Classes\Migration;
use Modules\Base\Entities\BaseModel;

class Ledger extends BaseModel
{

    protected $fillable = ['chart_id', 'category_id', 'name', 'slug', 'code', 'unused', 'is_system'];
    public $migrationDependancy = ['account_chart_of_account', 'account_ledger_category'];
    protected $table = "account_ledger";

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $table->increments('id');
        $table->integer('chart_id')->nullable();
        $table->integer('category_id')->nullable();
        $table->string('name')->nullable();
        $table->string('slug')->nullable();
        $table->integer('code')->nullable();
        $table->tinyInteger('unused')->default(true);
        $table->tinyInteger('is_system')->default(false);
    }

    public function post_migration(Blueprint $table)
    {
        if (Migration::checkKeyExist('account_ledger', 'chart_id')) {
            $table->foreign('chart_id')->references('id')->on('account_chart_of_account')->nullOnDelete();
        }
        if (Migration::checkKeyExist('account_ledger', 'category_id')) {
            $table->foreign('category_id')->references('id')->on('account_ledger_category')->nullOnDelete();
        }
    }

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

        parent::deleteRecord($id);

    }
}
