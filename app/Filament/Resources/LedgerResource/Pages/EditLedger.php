<?php

namespace Modules\Account\Filament\Resources\LedgerResource\Pages;

use Modules\Account\Filament\Resources\LedgerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLedger extends EditRecord
{
    protected static string $resource = LedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
