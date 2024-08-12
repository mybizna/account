<?php

namespace Modules\Account\Filament\Resources\PaymentRateResource\Pages;

use Modules\Account\Filament\Resources\PaymentRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentRates extends ListRecords
{
    protected static string $resource = PaymentRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
