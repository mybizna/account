<?php

namespace Modules\Account\Filament\Pages;

use Filament\Pages\Page;

class AccountDashboard extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'account::filament.pages.account-dashboard';

    protected static ?string $navigationGroup = 'Account';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = -5;
}
