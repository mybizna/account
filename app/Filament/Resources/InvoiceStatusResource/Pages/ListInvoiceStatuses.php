<?php

namespace Modules\Account\Filament\Resources\InvoiceStatusResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Account\Filament\Resources\InvoiceStatusResource;

class ListInvoiceStatuses extends ListRecords
{
    protected static string $resource = InvoiceStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
