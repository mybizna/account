<?php

namespace Modules\Account\Filament\Resources\OpeningBalanceResource\Pages;

use Modules\Account\Filament\Resources\OpeningBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOpeningBalance extends EditRecord
{
    protected static string $resource = OpeningBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
