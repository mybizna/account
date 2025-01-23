<?php

namespace Modules\Account\Filament\Clusters\Settings\Resources;

use Modules\Account\Models\LedgerCategory;
use Modules\Base\Filament\Resources\BaseResource;
use Modules\Account\Filament\Clusters\Settings\Settings;

class LedgerCategoryResource extends BaseResource
{
    protected static ?string $model = LedgerCategory::class;

    protected static ?string $cluster = Settings::class;

    protected static ?string $slug = 'account/ledger/category';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


}
