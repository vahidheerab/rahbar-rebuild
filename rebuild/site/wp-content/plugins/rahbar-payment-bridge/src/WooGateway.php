<?php

declare(strict_types=1);

namespace Rahbar\PaymentBridge;

final class WooGateway extends \WC_Payment_Gateway
{
    public const ID = 'rahbar_payment_fake';
    public const CALLBACK_API = 'rahbar_payment_bridge';

    public function __construct()
    {
        $this->id = self::ID;
        $this->method_title = 'پرداخت آزمایشی راهبر';
        $this->method_description = 'فقط برای تست محلی؛ هیچ مبلغی جابه‌جا نمی‌شود.';
        $this->title = 'پرداخت آزمایشی (بدون تراکنش واقعی)';
        $this->description = 'این روش فقط در محیط تست راهبر فعال است.';
        $this->has_fields = false;
        $this->supports = ['products'];
        $this->enabled = 'yes';
    }

    public function is_available(): bool
    {
        return Bridge::sandboxAllowed() && parent::is_available();
    }

    public function process_payment($order_id): array
    {
        $order = wc_get_order($order_id);
        if (!$order instanceof \WC_Order || !Bridge::sandboxAllowed()) {
            return ['result' => 'failure'];
        }
        $correlation = wp_generate_uuid4();
        $callback = add_query_arg([
            'order_id' => $order->get_id(),
            'order_key' => $order->get_order_key(),
        ], \WC()->api_request_url(self::CALLBACK_API));
        $request = new PaymentRequest($order->get_id(), (int) round((float) $order->get_total()), $order->get_currency(), $callback, $correlation);
        $adapter = Bridge::adapter();
        $session = $adapter->createPayment($request);
        $order->update_meta_data('_rahbar_payment_adapter', $adapter->id());
        $order->update_meta_data('_rahbar_payment_token', $session->token);
        $order->update_meta_data('_rahbar_payment_amount', $request->amount);
        $order->update_meta_data('_rahbar_payment_currency', $request->currency);
        $order->update_meta_data('_rahbar_payment_callback', $request->callbackUrl);
        $order->update_meta_data('_rahbar_payment_correlation', $correlation);
        $order->save();
        return ['result' => 'success', 'redirect' => $session->redirectUrl];
    }
}
