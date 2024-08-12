<?php

namespace Modules\Account\Filament\Resources\LedgerResource\Pages;

use Modules\Account\Filament\Resources\LedgerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLedgers extends ListRecords
{
    protected static string $resource = LedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
