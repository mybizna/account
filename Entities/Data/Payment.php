<?php

namespace Modules\Account\Entities\Data;

use Modules\Base\Classes\Datasetter;

class Payment
{

    public $ordering = 3;

    public function data(Datasetter $datasetter)
    {

        $datasetter->add_data('core', 'notification', 'slug', [
            "slug" => "account_payment_pending",
            "short" => 'Your payment {{ $receipt_no }} of {{ $amount }} paid by {{ $gateway_title }} is pending.',
            "medium" => 'Your payment {{ $receipt_no }} of {{ $amount }} paid by {{ $gateway_title }} is pending verification.',
            "enable_short" => true,
            "enable_medium" => true,
            "enable_lengthy" => true,
            "published" => true,
            "lengthy" => '
            Hi {{ $partner_name }},
            <br><br>
            <h3> Payment: {{ $receipt_no }} .</h3>
            <p> <b>Payment Title:</b> {{ $title }} .</p>
            <br><br>
            Your payment {{ $receipt_no }} of {{ $amount }} paid by {{ $gateway_title }} is pending verification.
            <br><br>
            If you need any help, please let us know so that we can assist you.
            <br><br>
            Thanks,',
        ]);

        $datasetter->add_data('core', 'notification', 'slug', [
            "slug" => "account_payment_paid",
            "short" => 'Your payment {{ $receipt_no }} of {{ $amount }} paid by {{ $gateway_title }} was successful.',
            "medium" => 'Your payment {{ $receipt_no }} of {{ $amount }} paid by {{ $gateway_title }} was successful.',
            "enable_short" => true,
            "enable_medium" => true,
            "enable_lengthy" => true,
            "published" => true,
            "lengthy" => '
            Hi {{ $partner_name }},
            <br><br>
            <h3> Receipt No: {{ $receipt_no }} .</h3>
            <p> <b> Title:</b> {{ $title }} .</p>
            <p> <b>Code:</b> {{ $code }} .</p>
            <p> <b>Gateway:</b> {{ $gateway_title }} .</p>
            <br><br>
            Your payment {{ receipt_no }} of {{ $amount }} paid by {{ $gateway_title }} was successful.
            <br><br>
            If you need any help, please let us know so that we can assist you.
            <br><br>
            Thanks,',
        ]);

        $datasetter->add_data('core', 'notification', 'slug', [
            "slug" => "account_payment_reversed",
            "short" => 'Your payment {{ $receipt_no }} of {{ $amount }} paid by {{ $gateway_title }} was reversed.',
            "medium" => 'Your payment {{ $receipt_no }} of {{ $amount }} paid by {{ $gateway_title }} was reversed.',
            "enable_short" => true,
            "enable_medium" => true,
            "enable_lengthy" => true,
            "published" => true,
            "lengthy" => '
            Hi {{ $partner_name }},
            <br><br>
            <h3> Receipt No: {{ $receipt_no }} .</h3>
            <p> <b> Title:</b> {{ $title }} .</p>
            <p> <b>Code:</b> {{ $code }} .</p>
            <p> <b>Gateway:</b> {{ $gateway_title }} .</p>
            <br><br>
            Your payment {{ $receipt_no }} of {{ $amount }} paid by {{ $gateway_title }} was reversed.
            <br><br>
            If you need any help, please let us know so that we can assist you.
            <br><br>
            Thanks,',
        ]);

        $datasetter->add_data('core', 'notification', 'slug', [
            "slug" => "account_payment_canceled",
            "short" => 'Your payment {{ $receipt_no }} of {{ $amount }} paid by {{ $gateway_title }} was canceled.',
            "medium" => 'Your payment {{ $receipt_no }} of {{ $amount }} paid by {{ $gateway_title }} was canceled.',
            "enable_short" => true,
            "enable_medium" => true,
            "enable_lengthy" => true,
            "published" => true,
            "lengthy" => '
            Hi {{ $partner_name }},
            <br><br>
            <h3> Receipt No: {{ $receipt_no }} .</h3>
            <p> <b> Title:</b> {{ $title }} .</p>
            <p> <b>Code:</b> {{ $code }} .</p>
            <p> <b>Gateway:</b> {{ $gateway_title }} .</p>
            <br><br>
            Your payment {{ $receipt_no }} of {{ $amount }} paid by {{ $gateway_title }} was canceled.
            <br><br>
            If you need any help, please let us know so that we can assist you.
            <br><br>
            Thanks,',
        ]);

    }
}
