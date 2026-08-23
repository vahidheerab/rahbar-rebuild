<?php

declare(strict_types=1);

use Rahbar\PaymentBridge\PaymentRequest;
use Rahbar\PaymentBridge\ProviderRegistry;
use Rahbar\PaymentBridge\Tests\FakeAdapter;

$root = dirname(__DIR__);
foreach (['PaymentRequest', 'PaymentSession', 'VerificationResult', 'ProviderAdapter', 'ProviderRegistry'] as $class) {
    require_once $root . '/src/' . $class . '.php';
}
require_once __DIR__ . '/FakeAdapter.php';

$assert = static function (bool $condition, string $message): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
};

$request = new PaymentRequest(42, 118000000, 'IRT', 'https://example.test/callback', 'test-correlation-42');
$adapter = new FakeAdapter('local-test-secret-value');
$registry = new ProviderRegistry();
$registry->register($adapter);
$assert($registry->get('fake') === $adapter, 'Registry did not return the registered adapter.');

$session = $adapter->createPayment($request);
$approved = [
    'token' => $session->token,
    'status' => 'approved',
    'amount' => '118000000',
    'currency' => 'IRT',
    'reference' => 'fake-reference-42',
];
$verified = $adapter->verifyPayment($approved, $request, $session->token);
$assert($verified->successful && $verified->reference === 'fake-reference-42', 'Approved payment was not verified.');

$mismatch = $approved;
$mismatch['amount'] = '117999999';
$assert($adapter->verifyPayment($mismatch, $request, $session->token)->category === 'payment_mismatch', 'Amount mismatch was accepted.');

$badToken = $approved;
$badToken['token'] = str_repeat('0', 64);
$assert($adapter->verifyPayment($badToken, $request, $session->token)->category === 'invalid_token', 'Invalid token was accepted.');

$declined = $approved;
$declined['status'] = 'declined';
$assert($adapter->verifyPayment($declined, $request, $session->token)->category === 'declined', 'Declined payment was accepted.');

echo "Rahbar Payment Bridge contract tests passed.\n";
