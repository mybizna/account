<?php

namespace Modules\Account\Filament\Resources\OpeningBalanceResource\Pages;

use Modules\Account\Filament\Resources\OpeningBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOpeningBalances extends ListRecords
{
    protected static string $resource = OpeningBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
