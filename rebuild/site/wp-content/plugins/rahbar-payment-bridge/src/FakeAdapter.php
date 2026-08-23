<?php

declare(strict_types=1);

namespace Rahbar\PaymentBridge;

final class FakeAdapter implements ProviderAdapter
{
    public function __construct(
        private readonly string $secret,
        private readonly string $sandboxUrl
    ) {
        if (strlen($secret) < 16 || filter_var($sandboxUrl, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('Fake adapter configuration is invalid.');
        }
    }

    public function id(): string
    {
        return 'fake';
    }

    public function createPayment(PaymentRequest $request): PaymentSession
    {
        $token = hash_hmac('sha256', $this->payload($request), $this->secret);
        return new PaymentSession($token, add_query_arg([
            'rahbar-payment-sandbox' => '1',
            'order_id' => $request->orderId,
            'token' => $token,
        ], $this->sandboxUrl));
    }

    public function verifyPayment(array $callback, PaymentRequest $request, string $sessionToken): VerificationResult
    {
        $expected = hash_hmac('sha256', $this->payload($request), $this->secret);
        $sentToken = is_string($callback['token'] ?? null) ? $callback['token'] : '';
        if (!hash_equals($expected, $sessionToken) || !hash_equals($sessionToken, $sentToken)) {
            return VerificationResult::failure('invalid_token');
        }
        if (($callback['status'] ?? null) !== 'approved') {
            return VerificationResult::failure('declined');
        }
        $amount = filter_var($callback['amount'] ?? null, FILTER_VALIDATE_INT);
        $currency = is_string($callback['currency'] ?? null) ? $callback['currency'] : '';
        if ($amount !== $request->amount || $currency !== $request->currency) {
            return VerificationResult::failure('payment_mismatch');
        }
        $reference = is_string($callback['reference'] ?? null) ? trim($callback['reference']) : '';
        return $reference === ''
            ? VerificationResult::failure('missing_reference')
            : VerificationResult::success($reference, $amount, $currency);
    }

    private function payload(PaymentRequest $request): string
    {
        return implode('|', [$request->orderId, $request->amount, $request->currency, $request->correlationId]);
    }
}
