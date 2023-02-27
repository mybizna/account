<?php

namespace Modules\Account\Entities\Data;

use Modules\Base\Classes\Datasetter;

class Invoice
{

    public $ordering = 3;

    public function data(Datasetter $datasetter)
    {

        $datasetter->add_data('core', 'notification', 'slug', [
            "slug" => "account_invoice_pending",
            "short" => 'Invoice: {{ $invoice_no }} with due on {{ date("d-m-Y", strtotime($due_date)) }} is pending and you need to make payment of {{ $amount }}.',
            "medium" => 'Invoice: {{ $invoice_no }} for [{{ $title }}] with due on {{ date("d-m-Y", strtotime($due_date)) }} is pending and you need to make payment of {{ $amount }}.',
            "enable_short" => true,
            "enable_medium" => true,
            "enable_lengthy" => true,
            "published" => true,
            "lengthy" => 'Hi {{ $partner_name }},
            <br><br>
            <h3> Invoice: {{ $invoice_no }} .</h3>
            <p> <b>Invoice Title:</b> {{ $title }} .</p>
            <br><br>
            This is a notice that an invoice has been generated on {{ date("j F Y", strtotime($date_created)) }} with due date {{ date("d-m-Y", strtotime($due_date)) }}.
            <br><br>
            Attached is a PDF with invoice details.
            <br><br>
            If you need any help, please let us know so that we can assist you.
            <br><br>
            Thanks,',
        ]);

        $datasetter->add_data('core', 'notification', 'slug', [
            "slug" => "account_invoice_partial",
            "short" => 'Invoice: {{ $invoice_no }} is partially paid with {{ $payment_amount }} with balance of {{ $balance }}.',
            "medium" => 'Invoice: {{ $invoice_no }} for [{{ $title }}] is partially paid with {{ $payment_amount }}  with balance of {{ $balance }}. It\'s due date is {{ date("d-m-Y", strtotime($due_date)) }}.',
            "enable_short" => true,
            "enable_medium" => true,
            "enable_lengthy" => true,
            "published" => true,
            "lengthy" => 'Hi {{ $partner_name }},
            <br><br>
            <h3> <b>Invoice No:</b> {{ $invoice_no }} .</h3>
            <p> <b>Invoice Title:</b> {{ $title }} .</p>
            <br><br>
            [{{ title }}]
            The invoice {{ $invoice_no }} has a partial payment of {{ $payment_amount }} with balance of {{ $balance }}.
            <br><br>
            Attached is a PDF with invoice details.
            <br><br>
            If you need any help, please let us know so that we can assist you.
            <br><br>
            Thanks,',
        ]);

        $datasetter->add_data('core', 'notification', 'slug', [
            "slug" => "account_invoice_paid",
            "short" => 'Invoice: {{ $invoice_no }} is fully paid Payment.',
            "medium" => 'Invoice: {{ $invoice_no }} for [{{ $title }}] is fully paid Payment.',
            "enable_short" => true,
            "enable_medium" => true,
            "enable_lengthy" => true,
            "published" => true,
            "lengthy" => 'Hi {{ $partner_name }},
            <br><br>
            <h3> Invoice: {{ $invoice_no }} .</h3>
            <p> <b>Invoice Title:</b> {{ $title }} .</p>
            <br><br>
            The invoice {{ $invoice_no }} with due date on {{ date("d-m-Y", strtotime($due_date)) }} is fully paid.
            <br><br>
            Attached is a PDF with invoice details.
            <br><br>
            If you need any help, please let us know so that we can assist you.
            <br><br>
            Thanks,',
        ]);

        $datasetter->add_data('core', 'notification', 'slug', [
            "slug" => "account_invoice_closed",
            "short" => 'Invoice: {{ $invoice_no }} with due date {{ date("d-m-Y", strtotime($due_date)) }} is closed.',
            "medium" => 'Invoice: {{ $invoice_no }} for [{{ $title }}] with due date {{ date("d-m-Y", strtotime($due_date)) }} is closed.',
            "enable_short" => true,
            "enable_medium" => true,
            "enable_lengthy" => true,
            "published" => true,
            "lengthy" => 'Hi {{ $partner_name }},
            <br><br>
            <h3> Invoice: {{ $invoice_no }} .</h3>
            <br><br>
            The invoice {{ $invoice_no }} with due date on {{ date("d-m-Y", strtotime($due_date)) }} is closed.
            <br><br>
            Attached is a PDF with invoice details.
            <br><br>
            If you need any help, please let us know so that we can assist you.
            <br><br>
            Thanks,',
        ]);

        $datasetter->add_data('core', 'notification', 'slug', [
            "slug" => "account_invoice_void",
            "short" => 'Invoice: {{ $invoice_no }} with due date {{ date("d-m-Y", strtotime($due_date)) }} is void.',
            "medium" => 'Invoice: {{ $invoice_no }} for [{{ $title }}] with due date {{ date("d-m-Y", strtotime($due_date)) }} is void.',
            "enable_short" => true,
            "enable_medium" => true,
            "enable_lengthy" => true,
            "published" => true,
            "lengthy" => 'Hi {{ $partner_name }},
            <br><br>
            <h3> Invoice: {{ $invoice_no }} .</h3>
            <br><br>
            The invoice {{ $invoice_no }} with due date on {{ date("d-m-Y", strtotime($due_date)) }} is void.
            <br><br>
            Attached is a PDF with invoice details.
            <br><br>
            If you need any help, please let us know so that we can assist you.
            <br><br>
            Thanks,',
        ]);

    }
}
