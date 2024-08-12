<?php

namespace Modules\Account\Filament\Resources\RateFileResource\Pages;

use Modules\Account\Filament\Resources\RateFileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRateFile extends EditRecord
{
    protected static string $resource = RateFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
