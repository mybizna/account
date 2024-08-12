<?php

namespace Modules\Account\Filament\Resources\GatewayAllowedinResource\Pages;

use Modules\Account\Filament\Resources\GatewayAllowedinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGatewayAllowedin extends EditRecord
{
    protected static string $resource = GatewayAllowedinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
