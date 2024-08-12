<?php

namespace Modules\Account\Filament\Resources\TransactionResource\Pages;

use Modules\Account\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;
}
