<?php
return  [
    'Xendit'=>App\PaymentGateway\XenditPayment::class,
    'PayPal'=>App\PaymentGateway\PaypalPayment::class,
    'Stripe'=>App\PaymentGateway\StripePayment::class,
    'Paystack'=>App\PaymentGateway\PaystackPayment::class,
    'RazorPay'=>App\PaymentGateway\RazorPayPayment::class,
    'PhonePe'=>App\PaymentGateway\PhonePePayment::class,
    'PayU'=>App\PaymentGateway\PayUPayment::class,
];
