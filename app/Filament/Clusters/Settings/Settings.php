<?php

namespace Modules\Account\Filament\Clusters\Settings;

use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Account';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'account/all-settings';

}
