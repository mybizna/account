<?php

namespace Modules\Account\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

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
        /*
    Filament::registerPages([
    AccountDashboard::class,
    ]);
     */

    }
}
