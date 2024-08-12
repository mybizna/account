<?php

namespace Modules\Account\Filament\Resources\GatewayRateResource\Pages;

use Modules\Account\Filament\Resources\GatewayRateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGatewayRate extends EditRecord
{
    protected static string $resource = GatewayRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
