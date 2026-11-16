<?php
/**
 * Photo importer: paste an image link in a tour (or the homepage banner setting) and the site
 * downloads the picture into the WordPress Media Library.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function kv_sideload( $url, $post_id = 0 ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = download_url( $url, 60 );
	if ( is_wp_error( $tmp ) ) {
		return $tmp;
	}
	$type = wp_get_image_mime( $tmp );
	$ext  = array( 'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif' );
	if ( ! $type || ! isset( $ext[ $type ] ) ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		return new WP_Error( 'kv_img', 'The link is not a direct JPG, PNG, WebP or GIF image. Open the photo, right-click it and choose "Copy image address".' );
	}
	$file = array( 'name' => sanitize_file_name( 'kv-photo-' . (int) $post_id . '-' . wp_rand( 1000, 9999 ) . '.' . $ext[ $type ] ), 'tmp_name' => $tmp );
	$id   = media_handle_sideload( $file, (int) $post_id );
	if ( is_wp_error( $id ) ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}
	return $id;
}

function kv_import_tour_photos( $post_id, $main, $gallery_raw ) {
	$map    = get_post_meta( $post_id, '_kv_import_map', true );
	$map    = is_array( $map ) ? $map : array();
	$errors = array();
	$prev   = (string) get_post_meta( $post_id, '_kv_main_link', true );
	$main   = esc_url_raw( trim( (string) $main ) );

	if ( '' !== $main && $main !== $prev ) {
		$id = kv_sideload( $main, $post_id );
		if ( is_wp_error( $id ) ) {
			$errors[] = $main . ' : ' . $id->get_error_message();
		} else {
			$map[ $main ] = (int) $id;
			set_post_thumbnail( $post_id, (int) $id );
		}
	}
	update_post_meta( $post_id, '_kv_main_link', $main );

	$cur = get_post_meta( $post_id, '_kv_gallery', true );
	$cur = is_array( $cur ) ? $cur : array();
	foreach ( kv_lines( $gallery_raw ) as $line ) {
		$u = esc_url_raw( $line );
		if ( ! $u || isset( $map[ $u ] ) ) {
			continue;
		}
		$id = kv_sideload( $u, $post_id );
		if ( is_wp_error( $id ) ) {
			$errors[] = $u . ' : ' . $id->get_error_message();
			continue;
		}
		$map[ $u ] = (int) $id;
		$cur[]     = (int) $id;
	}
	update_post_meta( $post_id, '_kv_gallery', array_values( array_unique( array_map( 'absint', $cur ) ) ) );
	update_post_meta( $post_id, '_kv_gallery_links', sanitize_textarea_field( (string) $gallery_raw ) );
	update_post_meta( $post_id, '_kv_import_map', $map );
	if ( $errors ) {
		set_transient( 'kv_img_err_' . get_current_user_id(), $errors, 120 );
	}
}

add_action( 'admin_notices', function () {
	$key = 'kv_img_err_' . get_current_user_id();
	$e   = get_transient( $key );
	if ( $e ) {
		delete_transient( $key );
		echo '<div class="notice notice-error"><p><strong>Some photo links could not be imported:</strong></p><ul>';
		foreach ( $e as $m ) {
			echo '<li>' . esc_html( $m ) . '</li>';
		}
		echo '</ul></div>';
	}
} );
