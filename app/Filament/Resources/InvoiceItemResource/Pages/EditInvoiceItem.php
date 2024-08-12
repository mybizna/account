<?php

namespace Modules\Account\Filament\Resources\InvoiceItemResource\Pages;

use Modules\Account\Filament\Resources\InvoiceItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoiceItem extends EditRecord
{
    protected static string $resource = InvoiceItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
