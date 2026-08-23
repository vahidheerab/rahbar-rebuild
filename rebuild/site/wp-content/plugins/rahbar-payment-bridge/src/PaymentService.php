<?php

declare(strict_types=1);

namespace Rahbar\PaymentBridge;

final class PaymentService
{
    public function __construct(private readonly TransactionStore $transactions)
    {
    }

    /** @param array<string, scalar|null> $callback */
    public function complete(\WC_Order $order, ProviderAdapter $adapter, array $callback): VerificationResult
    {
        if ($order->is_paid()) {
            return VerificationResult::success((string) $order->get_transaction_id(), (int) $order->get_meta('_rahbar_payment_amount'), (string) $order->get_meta('_rahbar_payment_currency'));
        }
        if (!$this->transactions->acquireOrderLock($order->get_id())) {
            return VerificationResult::failure('verification_locked');
        }

        try {
            $request = new PaymentRequest(
                $order->get_id(),
                (int) $order->get_meta('_rahbar_payment_amount'),
                (string) $order->get_meta('_rahbar_payment_currency'),
                (string) $order->get_meta('_rahbar_payment_callback'),
                (string) $order->get_meta('_rahbar_payment_correlation')
            );
            $result = $adapter->verifyPayment($callback, $request, (string) $order->get_meta('_rahbar_payment_token'));
            if (!$result->successful) {
                return $result;
            }
            if ($result->amount !== $request->amount || $result->currency !== $request->currency || $result->reference === null) {
                return VerificationResult::failure('payment_mismatch');
            }
            if (!$this->transactions->claimReference($adapter->id(), $result->reference, $order->get_id())) {
                return VerificationResult::failure('duplicate_transaction');
            }
            if (!$order->payment_complete($result->reference)) {
                $this->transactions->releaseReference($adapter->id(), $result->reference, $order->get_id());
                return VerificationResult::failure('payment_complete_failed');
            }
            $order->add_order_note('Rahbar Payment Bridge: sandbox payment verified.');
            do_action('rahbar_payment_verified', $order->get_id(), $adapter->id(), $result->reference);
            return $result;
        } finally {
            $this->transactions->releaseOrderLock($order->get_id());
        }
    }
}
