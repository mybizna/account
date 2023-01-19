<?php

namespace Modules\Account\Events;

use Illuminate\Queue\SerializesModels;

class InvoiceItemPaid
{
    use SerializesModels;

    public $invoice;

    public $invoice_item;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($invoice, $invoice_item)
    {
        $this->invoice = $invoice;
        $this->invoice_item = $invoice_item;
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
