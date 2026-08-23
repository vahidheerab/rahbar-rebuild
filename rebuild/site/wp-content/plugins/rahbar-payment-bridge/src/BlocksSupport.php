<?php

declare(strict_types=1);

namespace Rahbar\PaymentBridge;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class BlocksSupport extends AbstractPaymentMethodType
{
    protected $name = WooGateway::ID;

    public function initialize(): void
    {
        $this->settings = [];
    }

    public function is_active(): bool
    {
        return Bridge::sandboxAllowed();
    }

    public function get_payment_method_script_handles(): array
    {
        $relative = 'assets/js/checkout-block.js';
        wp_register_script(
            'rahbar-payment-bridge-blocks',
            plugins_url($relative, RAHBAR_PAYMENT_BRIDGE_FILE),
            ['wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-html-entities'],
            RAHBAR_PAYMENT_BRIDGE_VERSION,
            true
        );
        return ['rahbar-payment-bridge-blocks'];
    }

    public function get_payment_method_data(): array
    {
        return [
            'title' => 'پرداخت آزمایشی (بدون تراکنش واقعی)',
            'description' => 'فقط برای آزمون مسیر پرداخت در محیط محلی راهبر.',
            'supports' => ['products'],
        ];
    }
}
