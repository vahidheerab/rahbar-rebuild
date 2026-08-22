<?php
declare(strict_types=1);

if ( PHP_SAPI !== 'cli' ) { exit( 1 ); }
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$input = (string) ( $argv[1] ?? '' );
$payload = is_file( $input ) ? json_decode( (string) file_get_contents( $input ), true ) : null;
if ( ! is_array( $payload ) || 'rahbar-public-samples-v1' !== ( $payload['format'] ?? '' ) ) { fwrite( STDERR, "Invalid import payload.\n" ); exit( 2 ); }

function rahbar_import_terms( int $post_id, string $taxonomy, array $terms ): void {
	$ids = array();
	foreach ( $terms as $term ) {
		$name = sanitize_text_field( (string) ( $term['name'] ?? '' ) );
		$slug = sanitize_title( (string) ( $term['slug'] ?? '' ) );
		if ( '' === $name ) { continue; }
		$existing = term_exists( $slug, $taxonomy );
		if ( ! $existing ) { $existing = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) ); }
		if ( ! is_wp_error( $existing ) ) { $ids[] = (int) ( is_array( $existing ) ? $existing['term_id'] : $existing ); }
	}
	wp_set_object_terms( $post_id, $ids, $taxonomy, false );
}

function rahbar_import_thumbnail( int $post_id, int $source_id, ?array $image ): void {
	if ( ! $image || empty( $image['data_base64'] ) ) { return; }
	$existing = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_rahbar_legacy_attachment_for', 'meta_value' => (string) $source_id, 'fields' => 'ids', 'posts_per_page' => 1 ) );
	if ( $existing ) { set_post_thumbnail( $post_id, (int) $existing[0] ); return; }
	$binary = base64_decode( (string) $image['data_base64'], true );
	if ( false === $binary ) { return; }
	$upload = wp_upload_bits( sanitize_file_name( (string) ( $image['filename'] ?? "legacy-$source_id.jpg" ) ), null, $binary );
	if ( ! empty( $upload['error'] ) ) { throw new RuntimeException( (string) $upload['error'] ); }
	$attachment_id = wp_insert_attachment( array(
		'post_mime_type' => sanitize_mime_type( (string) ( $image['mime_type'] ?? 'image/jpeg' ) ),
		'post_title' => sanitize_text_field( (string) ( $image['alt'] ?? '' ) ),
		'post_status' => 'inherit',
	), $upload['file'], $post_id, true );
	if ( is_wp_error( $attachment_id ) ) { throw new RuntimeException( $attachment_id->get_error_message() ); }
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( (string) ( $image['alt'] ?? '' ) ) );
	update_post_meta( $attachment_id, '_rahbar_legacy_attachment_for', $source_id );
	set_post_thumbnail( $post_id, $attachment_id );
}

$summary = array( 'created' => 0, 'updated' => 0, 'posts' => 0, 'products' => 0, 'ids' => array() );
foreach ( (array) ( $payload['records'] ?? array() ) as $record ) {
	$source_id = absint( $record['source_id'] ?? 0 );
	$type = (string) ( $record['post_type'] ?? '' );
	if ( ! $source_id || ! in_array( $type, array( 'post', 'product' ), true ) ) { continue; }
	$found = get_posts( array( 'post_type' => $type, 'post_status' => 'any', 'meta_key' => '_rahbar_legacy_source_id', 'meta_value' => (string) $source_id, 'fields' => 'ids', 'posts_per_page' => 1 ) );
	$postarr = array(
		'ID' => $found ? (int) $found[0] : 0,
		'post_type' => $type,
		'post_status' => 'publish',
		'post_title' => sanitize_text_field( (string) ( $record['title'] ?? '' ) ),
		'post_name' => sanitize_title( (string) ( $record['slug'] ?? '' ) ),
		'post_excerpt' => wp_kses_post( (string) ( $record['excerpt'] ?? '' ) ),
		'post_content' => wp_kses_post( (string) ( $record['content'] ?? '' ) ),
	);
	if ( ! empty( $record['date_gmt'] ) ) { $postarr['post_date_gmt'] = sanitize_text_field( (string) $record['date_gmt'] ); }
	$post_id = wp_insert_post( wp_slash( $postarr ), true );
	if ( is_wp_error( $post_id ) ) { throw new RuntimeException( $post_id->get_error_message() ); }
	update_post_meta( $post_id, '_rahbar_legacy_source_id', $source_id );
	if ( 'post' === $type ) {
		rahbar_import_terms( $post_id, 'category', (array) ( $record['categories'] ?? array() ) );
		rahbar_import_terms( $post_id, 'post_tag', (array) ( $record['tags'] ?? array() ) );
		$summary['posts']++;
	} else {
		rahbar_import_terms( $post_id, 'product_cat', (array) ( $record['categories'] ?? array() ) );
		wp_set_object_terms( $post_id, 'simple', 'product_type', false );
		foreach ( array( 'regular_price', 'sale_price', 'price', 'stock_status', 'virtual' ) as $key ) {
			update_post_meta( $post_id, '_' . $key, sanitize_text_field( (string) ( $record[$key] ?? '' ) ) );
		}
		update_post_meta( $post_id, '_downloadable', 'no' );
		delete_post_meta( $post_id, '_downloadable_files' );
		$summary['products']++;
	}
	rahbar_import_thumbnail( $post_id, $source_id, is_array( $record['thumbnail'] ?? null ) ? $record['thumbnail'] : null );
	$found ? $summary['updated']++ : $summary['created']++;
	$summary['ids'][] = $post_id;
}

echo wp_json_encode( $summary, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . PHP_EOL;
