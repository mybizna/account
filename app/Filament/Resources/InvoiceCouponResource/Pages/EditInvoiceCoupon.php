<?php

namespace Modules\Account\Filament\Resources\InvoiceCouponResource\Pages;

use Modules\Account\Filament\Resources\InvoiceCouponResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoiceCoupon extends EditRecord
{
    protected static string $resource = InvoiceCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
