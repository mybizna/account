<?php

namespace Modules\Account\Filament\Resources\GatewayDisallowedinResource\Pages;

use Modules\Account\Filament\Resources\GatewayDisallowedinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGatewayDisallowedins extends ListRecords
{
    protected static string $resource = GatewayDisallowedinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
