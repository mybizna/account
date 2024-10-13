<?php

namespace Modules\Account\Filament\Resources\FinancialYearResource\Pages;

use Modules\Account\Filament\Resources\FinancialYearResource;
use Modules\Base\Filament\Resources\Pages\ListingBase;

// Class List that extends ListBase
class Listing extends ListingBase
{
    protected static string $resource = FinancialYearResource::class;
}
