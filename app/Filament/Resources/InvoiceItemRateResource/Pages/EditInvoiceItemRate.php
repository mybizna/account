<?php

namespace Modules\Account\Filament\Resources\InvoiceItemRateResource\Pages;

use Modules\Account\Filament\Resources\InvoiceItemRateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoiceItemRate extends EditRecord
{
    protected static string $resource = InvoiceItemRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
