<?php

namespace Modules\Account\Filament\Resources\InvoiceStatusResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Account\Filament\Resources\InvoiceStatusResource;

class EditInvoiceStatus extends EditRecord
{
    protected static string $resource = InvoiceStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
