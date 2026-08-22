<?php
/**
 * Idempotent local initializer for the Rebuild base pages.
 *
 * Run inside the Rebuild WordPress container. This file must not be served by
 * WordPress and intentionally lives outside the document root.
 */

declare(strict_types=1);

require '/var/www/html/wp-load.php';

require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( file_exists( WP_PLUGIN_DIR . '/rahbar-contact/rahbar-contact.php' ) && ! is_plugin_active( 'rahbar-contact/rahbar-contact.php' ) ) {
	$activation = activate_plugin( 'rahbar-contact/rahbar-contact.php' );
	if ( is_wp_error( $activation ) ) { fwrite( STDERR, 'rahbar-contact: ' . $activation->get_error_message() . "\n" ); exit( 1 ); }
}
if ( file_exists( WP_PLUGIN_DIR . '/rahbar-site-core/rahbar-site-core.php' ) && ! is_plugin_active( 'rahbar-site-core/rahbar-site-core.php' ) ) {
	$activation = activate_plugin( 'rahbar-site-core/rahbar-site-core.php' );
	if ( is_wp_error( $activation ) ) { fwrite( STDERR, 'rahbar-site-core: ' . $activation->get_error_message() . "\n" ); exit( 1 ); }
}

if ( ! defined( 'WP_CLI' ) && PHP_SAPI !== 'cli' ) {
	fwrite( STDERR, "CLI only.\n" );
	exit( 1 );
}

$pages = array(
	'blog'                               => array( 'وبلاگ', 'نوشته‌ها، آموزش‌ها و تازه‌ترین مطالب راهبر حساب.' ),
	'contact'                            => array( 'ارتباط با ما', 'راه‌های ارتباط با تیم راهبر حساب و اطلاعات تماس.' ),
	'services'                           => array( 'خدمات مشاوره مالیاتی', 'معرفی خدمات مالی، مالیاتی و حسابداری راهبر حساب.' ),
	'tax-consulting-services'            => array( 'خدمات مالی راهبر مالی', 'خدمات تخصصی مالی و مالیاتی ویژه کسب‌وکارها.' ),
	'questions'                          => array( 'کافه سؤال حسابداری', 'پرسش‌ها و پاسخ‌های تخصصی حسابداری و مالیاتی.' ),
	'easyinvoice'                        => array( 'نرم‌افزار راهبر سیستم', 'معرفی امکانات ایزی اینویس و اتصال به سامانه مودیان.' ),
	'customer-reviews'                   => array( 'تجربه دانشجویان', 'تجربه‌ها و دیدگاه‌های دانشجویان راهبر حساب.' ),
	'training-course-registration-guide' => array( 'راهنمای ثبت‌نام دوره‌ها', 'راهنمای انتخاب دوره، ثبت‌نام و دریافت دسترسی.' ),
	'order-tracking'                     => array( 'پیگیری سفارش', 'برای مشاهده وضعیت سفارش وارد حساب کاربری خود شوید.' ),
	'support'                            => array( 'پشتیبانی', 'مسیرهای دریافت پشتیبانی دوره‌ها و خدمات.' ),
	'careers'                            => array( 'فرصت‌های شغلی', 'فرصت‌های همکاری با مجموعه راهبر حساب.' ),
	'terms-and-conditions'               => array( 'قوانین و شرایط استفاده', 'قوانین استفاده از خدمات، دوره‌ها و وب‌سایت.' ),
	'cip-members'                        => array( 'برگزیده‌های راهبر حساب', 'معرفی اعضا و حسابداران برگزیده مجموعه.' ),
	'certificates'                       => array( 'مدارک و گواهینامه‌ها', 'اطلاعات مدارک و گواهینامه‌های راهبر حساب.' ),
	'licenses'                           => array( 'مجوزها', 'مجوزها و اعتبارنامه‌های مجموعه راهبر حساب.' ),
);

function rahbar_page_content( string $summary ): string {
	return sprintf(
		'<!-- wp:group {"className":"rahbar-base-page-intro","layout":{"type":"constrained"}} --><div class="wp-block-group rahbar-base-page-intro"><!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">%s</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>محتوای کامل این صفحه در مرحله بازسازی همان قابلیت تکمیل و با Legacy تطبیق داده می‌شود.</p><!-- /wp:paragraph --></div><!-- /wp:group -->',
		esc_html( $summary )
	);
}

$result = array();

foreach ( $pages as $slug => $page_definition ) {
	list( $title, $summary ) = $page_definition;
	$existing = get_page_by_path( $slug, OBJECT, 'page' );
	$postarr  = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_content' => rahbar_page_content( $summary ),
	);

	if ( $existing instanceof WP_Post ) {
		$postarr['ID'] = $existing->ID;
		$page_id       = wp_update_post( wp_slash( $postarr ), true );
		$operation     = 'updated';
	} else {
		$page_id   = wp_insert_post( wp_slash( $postarr ), true );
		$operation = 'created';
	}

	if ( is_wp_error( $page_id ) ) {
		fwrite( STDERR, $slug . ': ' . $page_id->get_error_message() . "\n" );
		exit( 1 );
	}

	$result[] = array( 'slug' => $slug, 'id' => (int) $page_id, 'operation' => $operation );
}

$woocommerce_pages = array(
	'shop'       => 'فروشگاه دوره‌ها',
	'cart'       => 'سبد خرید',
	'checkout'   => 'تسویه حساب',
	'my-account' => 'حساب کاربری',
);

foreach ( $woocommerce_pages as $slug => $title ) {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( ! $page instanceof WP_Post ) {
		fwrite( STDERR, "Required WooCommerce page is missing: {$slug}\n" );
		exit( 1 );
	}
	$updated = wp_update_post( array( 'ID' => $page->ID, 'post_title' => $title ), true );
	if ( is_wp_error( $updated ) ) {
		fwrite( STDERR, $slug . ': ' . $updated->get_error_message() . "\n" );
		exit( 1 );
	}
	$result[] = array( 'slug' => $slug, 'id' => (int) $page->ID, 'operation' => 'updated-title' );
}

update_option( 'permalink_structure', '/%postname%/' );
update_option( 'timezone_string', 'Asia/Tehran' );
update_option( 'WPLANG', 'fa_IR' );

// Course purchases must belong to a customer account so payment and course
// entitlement can be reconciled safely during cutover.
update_option( 'woocommerce_default_country', 'IR' );
update_option( 'woocommerce_allowed_countries', 'specific' );
update_option( 'woocommerce_specific_allowed_countries', array( 'IR' ) );
update_option( 'woocommerce_currency', 'IRT' );
update_option( 'woocommerce_enable_guest_checkout', 'no' );
update_option( 'woocommerce_enable_signup_and_login_from_checkout', 'yes' );
update_option( 'woocommerce_enable_myaccount_registration', 'yes' );

$blog_page = get_page_by_path( 'blog', OBJECT, 'page' );
$home_page = get_page_by_path( 'home', OBJECT, 'page' );
if ( ! $home_page instanceof WP_Post ) {
	$home_page_id = wp_insert_post(
		array(
			'post_title'   => 'خانه',
			'post_name'    => 'home',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		),
		true
	);
	if ( is_wp_error( $home_page_id ) ) {
		fwrite( STDERR, 'home: ' . $home_page_id->get_error_message() . "\n" );
		exit( 1 );
	}
	$home_page = get_post( $home_page_id );
}

if ( $blog_page instanceof WP_Post ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_page->ID );
	update_option( 'page_for_posts', $blog_page->ID );
}

global $wp_rewrite;
$wp_rewrite->init();
$wp_rewrite->set_permalink_structure( '/%postname%/' );
$wp_rewrite->flush_rules( false );

require_once ABSPATH . 'wp-admin/includes/misc.php';
$rewrite_rules = array(
	'<IfModule mod_rewrite.c>',
	'RewriteEngine On',
	'RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]',
	'RewriteBase /',
	'RewriteRule ^index\\.php$ - [L]',
	'RewriteCond %{REQUEST_FILENAME} !-f',
	'RewriteCond %{REQUEST_FILENAME} !-d',
	'RewriteRule . /index.php [L]',
	'</IfModule>',
);

if ( ! insert_with_markers( ABSPATH . '.htaccess', 'WordPress', $rewrite_rules ) ) {
	fwrite( STDERR, "Could not write WordPress rewrite rules.\n" );
	exit( 1 );
}

echo wp_json_encode(
	array(
		'status'    => 'ok',
		'pages'     => $result,
		'permalink' => get_option( 'permalink_structure' ),
		'timezone'  => get_option( 'timezone_string' ),
		'locale'    => get_option( 'WPLANG' ),
	),
	JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
) . PHP_EOL;
