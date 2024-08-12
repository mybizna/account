<?php

namespace Modules\Account\Filament\Resources\InvoiceItemRateResource\Pages;

use Modules\Account\Filament\Resources\InvoiceItemRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoiceItemRates extends ListRecords
{
    protected static string $resource = InvoiceItemRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
