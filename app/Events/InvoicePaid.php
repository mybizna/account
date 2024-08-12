<?php

namespace Modules\Account\Events;

use Illuminate\Queue\SerializesModels;

class InvoicePaid

{

    use SerializesModels;

    public $invoice;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
