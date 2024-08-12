<?php

namespace Modules\Account\Filament\Resources\GatewayAllowedinResource\Pages;

use Modules\Account\Filament\Resources\GatewayAllowedinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGatewayAllowedins extends ListRecords
{
    protected static string $resource = GatewayAllowedinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
