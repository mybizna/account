<?php

namespace Modules\Account\Filament\Resources\JournalResource\Pages;

use Modules\Account\Filament\Resources\JournalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJournal extends CreateRecord
{
    protected static string $resource = JournalResource::class;
}
