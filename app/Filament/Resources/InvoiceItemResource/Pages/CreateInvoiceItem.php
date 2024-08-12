<?php

namespace Modules\Account\Filament\Resources\InvoiceItemResource\Pages;

use Modules\Account\Filament\Resources\InvoiceItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoiceItem extends CreateRecord
{
    protected static string $resource = InvoiceItemResource::class;
}
