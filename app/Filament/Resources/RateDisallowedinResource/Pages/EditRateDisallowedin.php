<?php

namespace Modules\Account\Filament\Resources\RateDisallowedinResource\Pages;

use Modules\Account\Filament\Resources\RateDisallowedinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRateDisallowedin extends EditRecord
{
    protected static string $resource = RateDisallowedinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
