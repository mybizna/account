<?php

namespace Modules\Account\Filament\Resources\InvoiceResource\Pages;

use Modules\Account\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
}
