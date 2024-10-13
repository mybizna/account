<?php

namespace Modules\Account\Filament\Resources\PaymentResource\Pages;

use Modules\Account\Filament\Resources\PaymentResource;
use Modules\Base\Filament\Resources\Pages\ListingBase;

// Class List that extends ListBase
class Listing extends ListingBase
{
    protected static string $resource = PaymentResource::class;
}
