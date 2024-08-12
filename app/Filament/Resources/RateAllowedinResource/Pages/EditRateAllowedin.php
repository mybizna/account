<?php

namespace Modules\Account\Filament\Resources\RateAllowedinResource\Pages;

use Modules\Account\Filament\Resources\RateAllowedinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRateAllowedin extends EditRecord
{
    protected static string $resource = RateAllowedinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
