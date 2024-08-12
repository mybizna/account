<?php

namespace Modules\Account\Filament\Resources\FinancialYearResource\Pages;

use Modules\Account\Filament\Resources\FinancialYearResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFinancialYear extends CreateRecord
{
    protected static string $resource = FinancialYearResource::class;
}
