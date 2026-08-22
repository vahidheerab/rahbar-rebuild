<?php
declare(strict_types=1);

if ( PHP_SAPI !== 'cli' ) { exit( 1 ); }
require '/var/www/html/wp-load.php';

$ids = array_values( array_filter( array_map( 'absint', explode( ',', (string) ( $argv[1] ?? '' ) ) ) ) );
if ( ! $ids ) { fwrite( STDERR, "No source IDs supplied.\n" ); exit( 2 ); }

function rahbar_export_terms( int $post_id, string $taxonomy ): array {
	$terms = wp_get_post_terms( $post_id, $taxonomy );
	if ( is_wp_error( $terms ) ) { return array(); }
	if ( 'product_cat' === $taxonomy ) { $terms = array_values( array_filter( $terms, static fn( WP_Term $term ): bool => 'uncategorized' !== $term->slug ) ); }
	return array_map( static fn( WP_Term $term ): array => array(
		'name' => $term->name,
		'slug' => $term->slug,
	), $terms );
}

function rahbar_export_thumbnail( int $post_id ): ?array {
	$attachment_id = (int) get_post_thumbnail_id( $post_id );
	if ( ! $attachment_id ) { return null; }
	$path = get_attached_file( $attachment_id );
	if ( ! is_string( $path ) || ! is_file( $path ) || filesize( $path ) > 8 * MB_IN_BYTES ) { return null; }
	$data = file_get_contents( $path );
	if ( false === $data ) { return null; }
	return array(
		'filename' => sanitize_file_name( basename( $path ) ),
		'mime_type' => (string) get_post_mime_type( $attachment_id ),
		'alt' => (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
		'data_base64' => base64_encode( $data ),
	);
}

$records = array();
foreach ( $ids as $id ) {
	$post = get_post( $id );
	if ( ! $post || 'publish' !== $post->post_status || ! in_array( $post->post_type, array( 'post', 'product' ), true ) ) { continue; }
	$record = array(
		'source_id' => $id,
		'post_type' => $post->post_type,
		'title' => $post->post_title,
		'slug' => $post->post_name,
		'excerpt' => $post->post_excerpt,
		'content' => $post->post_content,
		'date_gmt' => $post->post_date_gmt,
		'thumbnail' => rahbar_export_thumbnail( $id ),
	);
	if ( 'post' === $post->post_type ) {
		$record['categories'] = rahbar_export_terms( $id, 'category' );
		$record['tags'] = rahbar_export_terms( $id, 'post_tag' );
	} else {
		$record['categories'] = rahbar_export_terms( $id, 'product_cat' );
		$record['regular_price'] = (string) get_post_meta( $id, '_regular_price', true );
		$record['sale_price'] = (string) get_post_meta( $id, '_sale_price', true );
		$record['price'] = (string) get_post_meta( $id, '_price', true );
		$record['stock_status'] = (string) get_post_meta( $id, '_stock_status', true );
		$record['virtual'] = (string) get_post_meta( $id, '_virtual', true );
	}
	$records[] = $record;
}

echo wp_json_encode( array(
	'format' => 'rahbar-public-samples-v1',
	'generated_at_utc' => gmdate( DATE_ATOM ),
	'records' => $records,
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
