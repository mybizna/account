<?php

namespace Modules\Account\Filament\Resources\RateResource\Pages;

use Modules\Account\Filament\Resources\RateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRates extends ListRecords
{
    protected static string $resource = RateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
