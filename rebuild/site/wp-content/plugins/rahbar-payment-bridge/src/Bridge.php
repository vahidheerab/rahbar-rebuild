<?php

declare(strict_types=1);

namespace Rahbar\PaymentBridge;

final class Bridge
{
    public static function sandboxAllowed(): bool
    {
        return wp_get_environment_type() !== 'production';
    }

    public static function adapter(): FakeAdapter
    {
        return new FakeAdapter(wp_salt('auth'), home_url('/'));
    }

    public static function register(): void
    {
        if (!self::sandboxAllowed()) {
            return;
        }
        add_filter('woocommerce_payment_gateways', static function (array $gateways): array {
            $gateways[] = WooGateway::class;
            return $gateways;
        });
        add_action('woocommerce_api_' . WooGateway::CALLBACK_API, [self::class, 'callback']);
        add_action('template_redirect', [self::class, 'sandboxPage']);
    }

    public static function callback(): void
    {
        $orderId = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
        $orderKey = isset($_GET['order_key']) && is_scalar($_GET['order_key']) ? wc_clean(wp_unslash($_GET['order_key'])) : '';
        $order = $orderId ? wc_get_order($orderId) : false;
        if (!$order instanceof \WC_Order || !hash_equals($order->get_order_key(), $orderKey) || $order->get_payment_method() !== WooGateway::ID) {
            wp_die('Invalid sandbox payment callback.', 'Payment error', ['response' => 400]);
        }
        $callback = [];
        foreach (['token', 'status', 'amount', 'currency', 'reference'] as $key) {
            $callback[$key] = isset($_GET[$key]) && is_scalar($_GET[$key]) ? sanitize_text_field(wp_unslash($_GET[$key])) : null;
        }
        $result = (new PaymentService(new TransactionStore()))->complete($order, self::adapter(), $callback);
        if (!$result->successful) {
            $order->add_order_note('Rahbar Payment Bridge sandbox failure: ' . $result->category);
            wc_add_notice('پرداخت آزمایشی تأیید نشد: ' . $result->category, 'error');
            wp_safe_redirect(wc_get_checkout_url());
            exit;
        }
        wp_safe_redirect($order->get_checkout_order_received_url());
        exit;
    }

    public static function sandboxPage(): void
    {
        if (!self::sandboxAllowed() || !isset($_GET['rahbar-payment-sandbox'])) {
            return;
        }
        $orderId = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
        $token = isset($_GET['token']) && is_scalar($_GET['token']) ? sanitize_text_field(wp_unslash($_GET['token'])) : '';
        $order = $orderId ? wc_get_order($orderId) : false;
        if (!$order instanceof \WC_Order || !hash_equals((string) $order->get_meta('_rahbar_payment_token'), $token)) {
            wp_die('Invalid sandbox session.', 'Payment error', ['response' => 400]);
        }
        $base = (string) $order->get_meta('_rahbar_payment_callback');
        $common = ['token' => $token, 'amount' => (int) $order->get_meta('_rahbar_payment_amount'), 'currency' => (string) $order->get_meta('_rahbar_payment_currency'), 'reference' => 'fake-' . $orderId];
        $approve = add_query_arg(array_merge($common, ['status' => 'approved']), $base);
        $decline = add_query_arg(array_merge($common, ['status' => 'declined']), $base);
        status_header(200);
        nocache_headers();
        echo '<!doctype html><html lang="fa" dir="rtl"><meta charset="utf-8"><title>Sandbox راهبر</title><style>body{font-family:sans-serif;max-width:640px;margin:10vh auto;padding:2rem}a{display:inline-block;padding:1rem 1.5rem;margin:.5rem;border-radius:.7rem;background:#65228f;color:#fff;text-decoration:none}.decline{background:#9b1c1c}</style><h1>درگاه آزمایشی راهبر</h1><p>هیچ مبلغ واقعی جابه‌جا نمی‌شود. نتیجه موردنظر را انتخاب کنید.</p><a href="' . esc_url($approve) . '">تأیید پرداخت آزمایشی</a><a class="decline" href="' . esc_url($decline) . '">رد پرداخت آزمایشی</a></html>';
        exit;
    }
}
