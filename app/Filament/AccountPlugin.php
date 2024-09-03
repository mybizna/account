<?php

namespace Modules\Account\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Modules\Account\Filament\Pages\AccountDashboard;

class AccountPlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'Account';
    }

    public function getId(): string
    {
        return 'account';
    }

    public function boot(Panel $panel): void
    {

        Filament::registerPages([
            AccountDashboard::class,
        ]);

        // Add "Account" to the top navigation
        $panel->navigationItems([
            NavigationItem::make()
                ->label('Account')
                ->url(AccountDashboard::getUrl())
                ->icon('heroicon-o-squares-2x2')
                ->sort(-1), // Adjust the order as needed
        ]);

    }
}
