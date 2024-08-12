<?php

namespace Modules\Account\Filament\Resources\FinancialYearResource\Pages;

use Modules\Account\Filament\Resources\FinancialYearResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinancialYears extends ListRecords
{
    protected static string $resource = FinancialYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
