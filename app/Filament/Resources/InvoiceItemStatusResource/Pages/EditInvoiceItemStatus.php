<?php

namespace Modules\Account\Filament\Resources\InvoiceItemStatusResource\Pages;

use Modules\Account\Filament\Resources\InvoiceItemStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoiceItemStatus extends EditRecord
{
    protected static string $resource = InvoiceItemStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
