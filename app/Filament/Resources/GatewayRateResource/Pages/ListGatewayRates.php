<?php

namespace Modules\Account\Filament\Resources\GatewayRateResource\Pages;

use Modules\Account\Filament\Resources\GatewayRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGatewayRates extends ListRecords
{
    protected static string $resource = GatewayRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
