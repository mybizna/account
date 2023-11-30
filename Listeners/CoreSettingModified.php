<?php

namespace Modules\Account\Listeners;

use Modules\Account\Classes\Journal;

class CoreSettingModified
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {

        $journal = new Journal();

        if ($event->module == 'core' && $event->name == 'default_currency' && $event->value != $event->oldvalue) {
            $journal->changeCurrency($event->oldvalue, $event->value);
        }
    }
}
