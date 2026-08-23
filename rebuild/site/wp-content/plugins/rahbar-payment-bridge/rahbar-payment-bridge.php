<?php
/**
 * Plugin Name: Rahbar Payment Bridge
 * Description: مرز پرداخت قابل توسعه و مستقل از ارائه‌دهنده برای ووکامرس راهبر.
 * Version: 0.1.0
 * Requires at least: 7.0
 * Requires PHP: 8.1
 * WC requires at least: 10.8
 * WC tested up to: 11.0
 * Requires Plugins: woocommerce
 * Text Domain: rahbar-payment-bridge
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

define('RAHBAR_PAYMENT_BRIDGE_VERSION', '0.1.0');
define('RAHBAR_PAYMENT_BRIDGE_FILE', __FILE__);
define('RAHBAR_PAYMENT_BRIDGE_PATH', plugin_dir_path(__FILE__));

require_once RAHBAR_PAYMENT_BRIDGE_PATH . 'src/PaymentRequest.php';
require_once RAHBAR_PAYMENT_BRIDGE_PATH . 'src/PaymentSession.php';
require_once RAHBAR_PAYMENT_BRIDGE_PATH . 'src/VerificationResult.php';
require_once RAHBAR_PAYMENT_BRIDGE_PATH . 'src/ProviderAdapter.php';
require_once RAHBAR_PAYMENT_BRIDGE_PATH . 'src/ProviderRegistry.php';
require_once RAHBAR_PAYMENT_BRIDGE_PATH . 'src/FakeAdapter.php';
require_once RAHBAR_PAYMENT_BRIDGE_PATH . 'src/TransactionStore.php';
require_once RAHBAR_PAYMENT_BRIDGE_PATH . 'src/PaymentService.php';

add_action('plugins_loaded', static function (): void {
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }
    require_once RAHBAR_PAYMENT_BRIDGE_PATH . 'src/WooGateway.php';
    require_once RAHBAR_PAYMENT_BRIDGE_PATH . 'src/Bridge.php';
    \Rahbar\PaymentBridge\Bridge::register();
}, 20);

add_action('before_woocommerce_init', static function (): void {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
    }
});

add_action('woocommerce_blocks_loaded', static function (): void {
    if (!class_exists(\Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType::class)) {
        return;
    }
    require_once RAHBAR_PAYMENT_BRIDGE_PATH . 'src/BlocksSupport.php';
    add_action('woocommerce_blocks_payment_method_type_registration', static function ($registry): void {
        $registry->register(new \Rahbar\PaymentBridge\BlocksSupport());
    });
});
