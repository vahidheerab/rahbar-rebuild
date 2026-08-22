<?php
/**
 * Rahbar theme bootstrap.
 *
 * Business logic belongs in dedicated plugins, not in this theme.
 *
 * @package Rahbar
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	static function (): void {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'style.css' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'post-thumbnails' );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		wp_enqueue_style(
			'rahbar-style',
			get_stylesheet_uri(),
			array(),
			(string) wp_get_theme()->get( 'Version' )
		);
		wp_enqueue_script(
			'rahbar-course-carousel',
			get_theme_file_uri( 'assets/js/course-carousel.js' ),
			array(),
			(string) wp_get_theme()->get( 'Version' ),
			array( 'in_footer' => true, 'strategy' => 'defer' )
		);
		wp_enqueue_script(
			'rahbar-accessibility',
			get_theme_file_uri( 'assets/js/accessibility.js' ),
			array(),
			(string) wp_get_theme()->get( 'Version' ),
			array( 'in_footer' => true, 'strategy' => 'defer' )
		);
	}
);

add_action(
	'init',
	static function (): void {
		register_block_pattern_category(
			'rahbar',
			array(
				'label' => __( 'رهبر', 'rahbar' ),
			)
		);
	}
);

add_filter(
	'woocommerce_product_single_add_to_cart_text',
	static function (): string {
		return __( 'افزودن به سبد خرید', 'rahbar' );
	}
);
