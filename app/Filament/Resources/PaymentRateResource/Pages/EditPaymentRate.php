<?php

namespace Modules\Account\Filament\Resources\PaymentRateResource\Pages;

use Modules\Account\Filament\Resources\PaymentRateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentRate extends EditRecord
{
    protected static string $resource = PaymentRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
