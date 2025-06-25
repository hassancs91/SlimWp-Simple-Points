<?php

// Stripe PHP bindings
// https://github.com/stripe/stripe-php

if (!defined('ABSPATH')) {
    exit;
}

// Don't redefine the Stripe classes if they already exist
if (!class_exists('Stripe\Stripe')) {
    // Load essential utility classes first
    require_once __DIR__ . '/Util/ApiVersion.php';
    require_once __DIR__ . '/Util/CaseInsensitiveArray.php';
    require_once __DIR__ . '/Util/LoggerInterface.php';
    require_once __DIR__ . '/Util/DefaultLogger.php';
    require_once __DIR__ . '/Util/RandomGenerator.php';
    require_once __DIR__ . '/Util/RequestOptions.php';
    require_once __DIR__ . '/Util/Set.php';
    require_once __DIR__ . '/Util/Util.php';
    require_once __DIR__ . '/Util/ObjectTypes.php';
    require_once __DIR__ . '/Util/EventTypes.php';
    
    // Load exception classes
    require_once __DIR__ . '/Exception/ExceptionInterface.php';
    require_once __DIR__ . '/Exception/ApiErrorException.php';
    require_once __DIR__ . '/Exception/ApiConnectionException.php';
    require_once __DIR__ . '/Exception/AuthenticationException.php';
    require_once __DIR__ . '/Exception/BadMethodCallException.php';
    require_once __DIR__ . '/Exception/CardException.php';
    require_once __DIR__ . '/Exception/IdempotencyException.php';
    require_once __DIR__ . '/Exception/InvalidArgumentException.php';
    require_once __DIR__ . '/Exception/InvalidRequestException.php';
    require_once __DIR__ . '/Exception/PermissionException.php';
    require_once __DIR__ . '/Exception/RateLimitException.php';
    require_once __DIR__ . '/Exception/SignatureVerificationException.php';
    require_once __DIR__ . '/Exception/UnexpectedValueException.php';
    require_once __DIR__ . '/Exception/UnknownApiErrorException.php';
    require_once __DIR__ . '/Exception/TemporarySessionExpiredException.php';
    
    // Load HTTP client classes
    require_once __DIR__ . '/HttpClient/ClientInterface.php';
    require_once __DIR__ . '/HttpClient/StreamingClientInterface.php';
    require_once __DIR__ . '/HttpClient/CurlClient.php';
    
    // Load basic core classes
    require_once __DIR__ . '/RequestTelemetry.php';
    require_once __DIR__ . '/ApiResponse.php';
    require_once __DIR__ . '/ApiRequestor.php';
    require_once __DIR__ . '/StripeObject.php';
    
    // Load API operation traits BEFORE classes that use them
    require_once __DIR__ . '/ApiOperations/Request.php';
    require_once __DIR__ . '/ApiOperations/All.php';
    require_once __DIR__ . '/ApiOperations/Create.php';
    require_once __DIR__ . '/ApiOperations/Delete.php';
    require_once __DIR__ . '/ApiOperations/NestedResource.php';
    require_once __DIR__ . '/ApiOperations/Retrieve.php';
    require_once __DIR__ . '/ApiOperations/SingletonRetrieve.php';
    require_once __DIR__ . '/ApiOperations/Update.php';
    
    // Now load classes that use the traits
    require_once __DIR__ . '/ApiResource.php';
    require_once __DIR__ . '/SingletonApiResource.php';
    require_once __DIR__ . '/Collection.php';
    require_once __DIR__ . '/SearchResult.php';
    
    // Load client interfaces (base first, then extended)
    require_once __DIR__ . '/BaseStripeClientInterface.php';
    require_once __DIR__ . '/StripeClientInterface.php';
    require_once __DIR__ . '/StripeStreamingClientInterface.php';
    require_once __DIR__ . '/BaseStripeClient.php';
    require_once __DIR__ . '/StripeClient.php';
    
    // Load essential resource classes for checkout
    require_once __DIR__ . '/Checkout/Session.php';
    require_once __DIR__ . '/PaymentIntent.php';
    require_once __DIR__ . '/PaymentMethod.php';
    require_once __DIR__ . '/Customer.php';
    require_once __DIR__ . '/Webhook.php';
    require_once __DIR__ . '/WebhookSignature.php';
    require_once __DIR__ . '/Event.php';
    
    // Finally load the main Stripe class
    require_once __DIR__ . '/Stripe.php';
}