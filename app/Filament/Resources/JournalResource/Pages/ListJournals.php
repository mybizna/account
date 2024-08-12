<?php

namespace Modules\Account\Filament\Resources\JournalResource\Pages;

use Modules\Account\Filament\Resources\JournalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJournals extends ListRecords
{
    protected static string $resource = JournalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
