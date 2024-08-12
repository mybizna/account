<?php

namespace Modules\Account\Filament\Resources\RateResource\Pages;

use Modules\Account\Filament\Resources\RateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRate extends EditRecord
{
    protected static string $resource = RateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
