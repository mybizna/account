<?php

namespace Modules\Account\Filament\Resources\RateFileResource\Pages;

use Modules\Account\Filament\Resources\RateFileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRateFiles extends ListRecords
{
    protected static string $resource = RateFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
