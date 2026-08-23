<?php

declare(strict_types=1);

namespace Rahbar\PaymentBridge;

interface ProviderAdapter
{
    public function id(): string;

    public function createPayment(PaymentRequest $request): PaymentSession;

    /** @param array<string, scalar|null> $callback */
    public function verifyPayment(array $callback, PaymentRequest $request, string $sessionToken): VerificationResult;
}
