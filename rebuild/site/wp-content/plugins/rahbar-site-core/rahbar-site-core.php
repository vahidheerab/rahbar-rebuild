<?php
/**
 * Plugin Name: Rahbar Site Core
 * Description: رفتارهای عمومی و مستقل از پوسته برای Rebuild راهبر حساب.
 * Version: 0.1.0
 * Requires at least: 7.0
 * Requires PHP: 8.1
 */
declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_filter( 'woocommerce_product_add_to_cart_text', static fn(): string => 'مشاهده و ثبت‌نام' );
add_filter( 'woocommerce_catalog_orderby', static function ( array $options ): array {
	$labels = array( 'menu_order' => 'مرتب‌سازی پیشنهادی', 'popularity' => 'محبوب‌ترین‌ها', 'rating' => 'بالاترین امتیاز', 'date' => 'جدیدترین‌ها', 'price' => 'قیمت: کم به زیاد', 'price-desc' => 'قیمت: زیاد به کم' );
	foreach ( $labels as $key => $label ) { if ( isset( $options[ $key ] ) ) { $options[ $key ] = $label; } }
	return $options;
} );

function rahbar_translate_woocommerce_string( string $translation, string $text ): string {
	$translations = array( 'Home' => 'خانه', 'Default sorting' => 'مرتب‌سازی پیشنهادی', 'Sale!' => 'تخفیف', 'Description' => 'توضیحات', 'Showing the single result' => 'نمایش یک نتیجه', 'Showing all %d results' => 'نمایش هر %d نتیجه', 'Showing %1$d–%2$d of %3$d results' => 'نمایش %1$d تا %2$d از %3$d نتیجه' );
	return $translations[ $text ] ?? $translation;
}
add_filter( 'gettext_woocommerce', 'rahbar_translate_woocommerce_string', 10, 2 );
add_filter( 'gettext', static function ( string $translation, string $text, string $domain ): string {
	return 'woocommerce' === $domain ? rahbar_translate_woocommerce_string( $translation, $text ) : $translation;
}, 10, 3 );
add_filter( 'ngettext_woocommerce', static function ( string $translation, string $single, string $plural, int $number ): string {
	if ( 'Showing all %d results' === $plural || 'Showing all %d results' === $single ) { return sprintf( 'نمایش هر %d نتیجه', $number ); }
	return $translation;
}, 10, 4 );
add_filter( 'woocommerce_sale_flash', static fn(): string => '<span class="onsale">تخفیف</span>' );
add_filter( 'woocommerce_result_count', static function ( string $html ): string {
	$total = (int) wc_get_loop_prop( 'total' );
	return sprintf( '<p class="woocommerce-result-count">نمایش هر %s نتیجه</p>', esc_html( number_format_i18n( $total ) ) );
} );
add_filter( 'woocommerce_breadcrumb_defaults', static function ( array $defaults ): array {
	$defaults['home'] = 'خانه';
	return $defaults;
} );
