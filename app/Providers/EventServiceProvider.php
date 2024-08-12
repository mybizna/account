<?php

namespace Modules\Account\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Core\Events\Setting;
use Modules\Account\Listeners\CoreSettingModified;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Setting::class => [
            CoreSettingModified::class,
        ],
    ];

}
