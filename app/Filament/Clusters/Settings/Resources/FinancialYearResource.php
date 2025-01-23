<?php

namespace Modules\Account\Filament\Clusters\Settings\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Modules\Account\Filament\Clusters\Settings\Settings;
use Modules\Account\Models\FinancialYear;
use Modules\Base\Filament\Resources\BaseResource;

class FinancialYearResource extends BaseResource
{
    protected static ?string $model = FinancialYear::class;

    protected static ?string $cluster = Settings::class;

    protected static ?string $slug = 'account/financial_year';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Financial Year';

    protected static ?int $navigationSort = 1;




}
