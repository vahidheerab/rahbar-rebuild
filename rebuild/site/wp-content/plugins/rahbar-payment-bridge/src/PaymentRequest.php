<?php

declare(strict_types=1);

namespace Rahbar\PaymentBridge;

final class PaymentRequest
{
    public function __construct(
        public readonly int $orderId,
        public readonly int $amount,
        public readonly string $currency,
        public readonly string $callbackUrl,
        public readonly string $correlationId
    ) {
        if ($orderId < 1 || $amount < 1) {
            throw new \InvalidArgumentException('Order ID and amount must be positive integers.');
        }
        if (!preg_match('/^[A-Z]{3,8}$/', $currency)) {
            throw new \InvalidArgumentException('Currency must be an uppercase code.');
        }
        if (filter_var($callbackUrl, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('Callback URL is invalid.');
        }
        if ($correlationId === '' || strlen($correlationId) > 64) {
            throw new \InvalidArgumentException('Correlation ID is invalid.');
        }
    }
}
