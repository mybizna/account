<?php

namespace Modules\Account\Filament\Resources\LedgerCategoryResource\Pages;

use Modules\Account\Filament\Resources\LedgerCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLedgerCategories extends ListRecords
{
    protected static string $resource = LedgerCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
