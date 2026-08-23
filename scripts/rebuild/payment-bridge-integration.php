<?php

declare(strict_types=1);

require '/var/www/html/wp-load.php';

use Rahbar\PaymentBridge\Bridge;
use Rahbar\PaymentBridge\PaymentRequest;
use Rahbar\PaymentBridge\PaymentService;
use Rahbar\PaymentBridge\TransactionStore;
use Rahbar\PaymentBridge\WooGateway;

if (!class_exists(WooGateway::class) || !Bridge::sandboxAllowed()) {
    fwrite(STDERR, "Payment Bridge sandbox is not available.\n");
    exit(1);
}

foreach (['new_order', 'cancelled_order', 'failed_order', 'customer_on_hold_order', 'customer_processing_order', 'customer_completed_order', 'customer_invoice'] as $emailId) {
    add_filter('woocommerce_email_enabled_' . $emailId, '__return_false');
}

$orders = [];
$store = new TransactionStore();
$reference = 'integration-reference-' . wp_generate_uuid4();
$verifiedEvents = 0;

add_action('rahbar_payment_verified', static function () use (&$verifiedEvents): void {
    $verifiedEvents++;
});

$assert = static function (bool $condition, string $message): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
};

$prepare = static function () use (&$orders): array {
    $order = wc_create_order();
    if (is_wp_error($order)) {
        throw new RuntimeException($order->get_error_message());
    }
    $orders[] = $order;
    $order->set_currency('IRT');
    $order->set_total(1000);
    $order->set_payment_method(WooGateway::ID);
    $correlation = wp_generate_uuid4();
    $callback = home_url('/?wc-api=' . WooGateway::CALLBACK_API);
    $request = new PaymentRequest($order->get_id(), 1000, 'IRT', $callback, $correlation);
    $adapter = Bridge::adapter();
    $session = $adapter->createPayment($request);
    $order->update_meta_data('_rahbar_payment_adapter', $adapter->id());
    $order->update_meta_data('_rahbar_payment_token', $session->token);
    $order->update_meta_data('_rahbar_payment_amount', 1000);
    $order->update_meta_data('_rahbar_payment_currency', 'IRT');
    $order->update_meta_data('_rahbar_payment_callback', $callback);
    $order->update_meta_data('_rahbar_payment_correlation', $correlation);
    $order->save();
    return [$order, $adapter, $session];
};

try {
    [$first, $adapter, $session] = $prepare();
    $approved = ['token' => $session->token, 'status' => 'approved', 'amount' => '1000', 'currency' => 'IRT', 'reference' => $reference];
    $service = new PaymentService($store);
    $result = $service->complete($first, $adapter, $approved);
    $assert($result->successful && $first->is_paid(), 'Approved callback did not complete payment.');
    $assert($verifiedEvents === 1, 'Verified payment event was not emitted exactly once.');

    $repeat = $service->complete(wc_get_order($first->get_id()), $adapter, $approved);
    $assert($repeat->successful && $repeat->reference === $reference, 'Repeated callback was not idempotent.');
    $assert($verifiedEvents === 1, 'Repeated callback emitted a duplicate verified payment event.');

    [$second, $secondAdapter, $secondSession] = $prepare();
    $duplicate = ['token' => $secondSession->token, 'status' => 'approved', 'amount' => '1000', 'currency' => 'IRT', 'reference' => $reference];
    $duplicateResult = $service->complete($second, $secondAdapter, $duplicate);
    $assert(!$duplicateResult->successful && $duplicateResult->category === 'duplicate_transaction', 'Cross-order duplicate reference was accepted.');

    [$third, $thirdAdapter, $thirdSession] = $prepare();
    $mismatch = ['token' => $thirdSession->token, 'status' => 'approved', 'amount' => '999', 'currency' => 'IRT', 'reference' => 'mismatch-' . $reference];
    $mismatchResult = $service->complete($third, $thirdAdapter, $mismatch);
    $assert(!$mismatchResult->successful && $mismatchResult->category === 'payment_mismatch', 'Amount mismatch was accepted.');

    echo "Rahbar Payment Bridge integration tests passed.\n";
} finally {
    $store->releaseReference('fake', $reference, isset($first) ? $first->get_id() : 0);
    foreach ($orders as $order) {
        $order->delete(true);
    }
}
