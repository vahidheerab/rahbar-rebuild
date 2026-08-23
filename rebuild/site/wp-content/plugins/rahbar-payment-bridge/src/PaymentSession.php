<?php

declare(strict_types=1);

namespace Rahbar\PaymentBridge;

final class PaymentSession
{
    public function __construct(
        public readonly string $token,
        public readonly string $redirectUrl
    ) {
        if ($token === '' || strlen($token) > 512) {
            throw new \InvalidArgumentException('Payment token is invalid.');
        }
        if (filter_var($redirectUrl, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('Payment redirect URL is invalid.');
        }
    }
}
