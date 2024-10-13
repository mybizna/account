<?php

namespace Modules\Account\Filament\Resources\JournalResource\Pages;

use Modules\Account\Filament\Resources\JournalResource;
use Modules\Base\Filament\Resources\Pages\ListingBase;

// Class List that extends ListBase
class Listing extends ListingBase
{
    protected static string $resource = JournalResource::class;
}
