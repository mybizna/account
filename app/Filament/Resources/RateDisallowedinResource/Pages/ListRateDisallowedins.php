<?php

namespace Modules\Account\Filament\Resources\RateDisallowedinResource\Pages;

use Modules\Account\Filament\Resources\RateDisallowedinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRateDisallowedins extends ListRecords
{
    protected static string $resource = RateDisallowedinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
