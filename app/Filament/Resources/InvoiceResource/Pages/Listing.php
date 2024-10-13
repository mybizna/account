<?php

namespace Modules\Account\Filament\Resources\InvoiceResource\Pages;

use Modules\Account\Filament\Resources\InvoiceResource;
use Modules\Base\Filament\Resources\Pages\ListingBase;

// Class List that extends ListBase
class Listing extends ListingBase
{
    protected static string $resource = InvoiceResource::class;
}
