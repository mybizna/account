<?php

namespace Modules\Account\Filament\Resources\GatewayDisallowedinResource\Pages;

use Modules\Account\Filament\Resources\GatewayDisallowedinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGatewayDisallowedin extends EditRecord
{
    protected static string $resource = GatewayDisallowedinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
