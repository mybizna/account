<?php

namespace Modules\Account\Filament\Resources\TransactionResource\Pages;

use Modules\Account\Filament\Resources\TransactionResource;
use Modules\Base\Filament\Resources\Pages\ListingBase;

// Class List that extends ListBase
class Listing extends ListingBase
{
    protected static string $resource = TransactionResource::class;
}
