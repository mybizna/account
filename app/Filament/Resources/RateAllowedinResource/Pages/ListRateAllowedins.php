<?php

namespace Modules\Account\Filament\Resources\RateAllowedinResource\Pages;

use Modules\Account\Filament\Resources\RateAllowedinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRateAllowedins extends ListRecords
{
    protected static string $resource = RateAllowedinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
