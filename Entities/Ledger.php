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

    protected $can_delete = "false";

    /**
     * List of fields for managing postings.
     *
     * @param Blueprint $table
     * @return void
     */
    public function migration(Blueprint $table)
    {
        $table->increments('id');
        $table->foreignId('chart_id')->nullable();
        $table->foreignId('category_id')->nullable();
        $table->string('name')->nullable();
        $table->string('slug')->nullable();
        $table->integer('code')->nullable();
        $table->tinyInteger('unused')->default(1);
        $table->tinyInteger('is_system')->default(0);
    }

    public function post_migration(Blueprint $table)
    {
        Migration::addForeign($table, 'account_chart_of_account', 'chart_id');
        Migration::addForeign($table, 'account_ledger_category', 'category_id');
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
