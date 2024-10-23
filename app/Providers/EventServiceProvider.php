<?php

namespace Modules\Account\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Account\Listeners\CoreSettingModified;
use Modules\Core\Events\Setting;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Setting::class => [
            CoreSettingModified::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     *
     * @return void
     */
    protected function configureEmailVerification(): void
    {

    }

}
