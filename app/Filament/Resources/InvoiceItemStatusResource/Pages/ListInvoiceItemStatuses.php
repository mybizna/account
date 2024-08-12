<?php

namespace Modules\Account\Filament\Resources\InvoiceItemStatusResource\Pages;

use Modules\Account\Filament\Resources\InvoiceItemStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoiceItemStatuses extends ListRecords
{
    protected static string $resource = InvoiceItemStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
