<?php

declare(strict_types=1);

namespace Rahbar\PaymentBridge;

final class TransactionStore
{
    private const LOCK_TTL = 300;

    public function acquireOrderLock(int $orderId): bool
    {
        $key = 'rahbar_payment_lock_' . $orderId;
        if (add_option($key, time(), '', false)) {
            return true;
        }
        $created = (int) get_option($key, 0);
        if ($created > 0 && time() - $created > self::LOCK_TTL) {
            delete_option($key);
            return add_option($key, time(), '', false);
        }
        return false;
    }

    public function releaseOrderLock(int $orderId): void
    {
        delete_option('rahbar_payment_lock_' . $orderId);
    }

    public function claimReference(string $adapterId, string $reference, int $orderId): bool
    {
        $key = 'rahbar_payment_tx_' . hash('sha256', $adapterId . '|' . $reference);
        if (add_option($key, $orderId, '', false)) {
            return true;
        }
        return (int) get_option($key, 0) === $orderId;
    }

    public function releaseReference(string $adapterId, string $reference, int $orderId): void
    {
        $key = 'rahbar_payment_tx_' . hash('sha256', $adapterId . '|' . $reference);
        if ((int) get_option($key, 0) === $orderId) {
            delete_option($key);
        }
    }
}
