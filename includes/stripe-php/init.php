<?php

// Stripe PHP bindings
// https://github.com/stripe/stripe-php

if (!defined('ABSPATH')) {
    exit;
}

// Don't redefine the Stripe classes if they already exist
if (!class_exists('Stripe\Stripe')) {
    require_once __DIR__ . '/Stripe.php';
}
