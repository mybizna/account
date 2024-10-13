<?php

namespace Modules\Account\Filament\Resources\InvoiceItemResource\Pages;

use Modules\Account\Filament\Resources\InvoiceItemResource;
use Modules\Base\Filament\Resources\Pages\ListingBase;

// Class List that extends ListBase
class Listing extends ListingBase
{
    protected static string $resource = InvoiceItemResource::class;
}
