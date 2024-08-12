<?php

namespace Modules\Account\Filament\Resources\InvoiceCouponResource\Pages;

use Modules\Account\Filament\Resources\InvoiceCouponResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoiceCoupons extends ListRecords
{
    protected static string $resource = InvoiceCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
