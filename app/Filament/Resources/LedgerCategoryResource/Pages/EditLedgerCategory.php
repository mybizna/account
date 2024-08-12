<?php

namespace Modules\Account\Filament\Resources\LedgerCategoryResource\Pages;

use Modules\Account\Filament\Resources\LedgerCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLedgerCategory extends EditRecord
{
    protected static string $resource = LedgerCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
