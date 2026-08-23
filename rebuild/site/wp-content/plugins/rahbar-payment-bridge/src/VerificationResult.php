<?php

declare(strict_types=1);

namespace Rahbar\PaymentBridge;

final class VerificationResult
{
    private function __construct(
        public readonly bool $successful,
        public readonly string $category,
        public readonly ?string $reference,
        public readonly ?int $amount,
        public readonly ?string $currency
    ) {
    }

    public static function success(string $reference, int $amount, string $currency): self
    {
        if ($reference === '' || strlen($reference) > 100 || $amount < 1) {
            throw new \InvalidArgumentException('Verified payment identity is invalid.');
        }
        return new self(true, 'verified', $reference, $amount, $currency);
    }

    public static function failure(string $category): self
    {
        if (!preg_match('/^[a-z0-9_]{3,64}$/', $category)) {
            throw new \InvalidArgumentException('Failure category is invalid.');
        }
        return new self(false, $category, null, null, null);
    }
}
