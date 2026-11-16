<?php
/**
 * Destination photos: downloads the free-licence Wikimedia Commons photos into the WordPress Media Library
 * once, so the website serves them from its own server instead of linking to Wikimedia on every page view.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function kv_photo_store() {
	$o = get_option( 'kv_photo_ids', array() );
	return is_array( $o ) ? $o : array();
}

/* Wikimedia asks every program to identify itself with a descriptive User-Agent. */
function kv_wm_user_agent() {
	return 'KashmiriVenturesWebsite/1.0 (' . home_url( '/' ) . '; ' . get_option( 'admin_email' ) . ') WordPress/' . get_bloginfo( 'version' );
}

function kv_wm_api( $args ) {
	$url = add_query_arg( array_merge( array( 'action' => 'query', 'format' => 'json', 'formatversion' => 2 ), $args ), 'https://commons.wikimedia.org/w/api.php' );
	$r   = wp_remote_get( $url, array( 'timeout' => 25, 'user-agent' => kv_wm_user_agent() ) );
	if ( is_wp_error( $r ) ) {
		return $r;
	}
	$code = (int) wp_remote_retrieve_response_code( $r );
	if ( 200 !== $code ) {
		return new WP_Error( 'kv_wm', 'Wikimedia answered with HTTP ' . $code );
	}
	$d = json_decode( wp_remote_retrieve_body( $r ), true );
	return is_array( $d ) ? $d : new WP_Error( 'kv_wm', 'Wikimedia sent an unreadable answer.' );
}

/* Finds the real file on Commons. Tries the exact file name first, then a search by destination name. */
function kv_wm_resolve( $file, $label ) {
	$d = kv_wm_api( array( 'titles' => 'File:' . $file, 'prop' => 'imageinfo', 'iiprop' => 'url|mime', 'iiurlwidth' => 1920 ) );
	if ( is_wp_error( $d ) ) {
		return $d;
	}
	if ( ! empty( $d['query']['pages'][0] ) && empty( $d['query']['pages'][0]['missing'] ) && ! empty( $d['query']['pages'][0]['imageinfo'][0] ) ) {
		$p  = $d['query']['pages'][0];
		$ii = $p['imageinfo'][0];
		return array( 'title' => preg_replace( '/^File:/', '', $p['title'] ), 'url' => ! empty( $ii['thumburl'] ) ? $ii['thumburl'] : $ii['url'], 'replaced' => false );
	}
	$d = kv_wm_api( array( 'generator' => 'search', 'gsrsearch' => $label . ' filetype:bitmap', 'gsrnamespace' => 6, 'gsrlimit' => 8, 'prop' => 'imageinfo', 'iiprop' => 'url|mime|size', 'iiurlwidth' => 1920 ) );
	if ( is_wp_error( $d ) ) {
		return $d;
	}
	if ( ! empty( $d['query']['pages'] ) ) {
		foreach ( $d['query']['pages'] as $p ) {
			$ii = isset( $p['imageinfo'][0] ) ? $p['imageinfo'][0] : array();
			if ( $ii && isset( $ii['mime'] ) && 'image/jpeg' === $ii['mime'] && ! empty( $ii['width'] ) && $ii['width'] >= 1400 && $ii['width'] > $ii['height'] ) {
				return array( 'title' => preg_replace( '/^File:/', '', $p['title'] ), 'url' => ! empty( $ii['thumburl'] ) ? $ii['thumburl'] : $ii['url'], 'replaced' => true );
			}
		}
	}
	return new WP_Error( 'kv_wm', 'File not found on Wikimedia Commons.' );
}

function kv_import_catalog_photos( $budget = 45 ) {
	@set_time_limit( 180 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	$start = time();
	$store = kv_photo_store();
	$res   = array( 'ok' => array(), 'replaced' => array(), 'errors' => array(), 'left' => 0 );
	$ua    = function () {
		return kv_wm_user_agent();
	};
	add_filter( 'http_headers_useragent', $ua );
	foreach ( kv_photo_catalog() as $key => $c ) {
		if ( ! empty( $store[ $key ]['id'] ) && get_post( (int) $store[ $key ]['id'] ) ) {
			continue;
		}
		if ( time() - $start > $budget ) {
			$res['left']++;
			continue;
		}
		$found = kv_wm_resolve( $c[0], $c[1] );
		if ( is_wp_error( $found ) ) {
			$res['errors'][] = $c[1] . ': ' . $found->get_error_message();
			continue;
		}
		$id = kv_sideload( $found['url'], 0 );
		if ( is_wp_error( $id ) ) {
			$res['errors'][] = $c[1] . ': ' . $id->get_error_message();
			continue;
		}
		wp_update_post( array( 'ID' => (int) $id, 'post_title' => $c[1] ) );
		update_post_meta( (int) $id, '_wp_attachment_image_alt', $c[1] );
		$store[ $key ] = array( 'id' => (int) $id, 'title' => $found['title'] );
		update_option( 'kv_photo_ids', $store, false );
		$res['ok'][] = $c[1];
		if ( $found['replaced'] ) {
			$res['replaced'][] = $c[1] . ' (' . $found['title'] . ')';
		}
	}
	remove_filter( 'http_headers_useragent', $ua );
	return $res;
}

function kv_photos_missing() {
	$store = kv_photo_store();
	$n     = 0;
	foreach ( kv_photo_catalog() as $key => $c ) {
		if ( empty( $store[ $key ]['id'] ) || ! get_post( (int) $store[ $key ]['id'] ) ) {
			$n++;
		}
	}
	return $n;
}

function kv_photo_import_form( $label = 'Download destination photos now' ) {
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline">';
	wp_nonce_field( 'kv_photos' );
	echo '<input type="hidden" name="action" value="kv_import_photos"><button class="button button-primary">' . esc_html( $label ) . '</button></form>';
}

add_action( 'admin_post_kv_import_photos', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You are not allowed to do this.' );
	}
	check_admin_referer( 'kv_photos' );
	$r   = kv_import_catalog_photos();
	$msg = count( $r['ok'] ) . ' photo(s) downloaded into the Media Library.';
	if ( $r['replaced'] ) {
		$msg .= ' Exact file not found, so a matching Commons photo was used for: ' . implode( '; ', $r['replaced'] ) . '.';
	}
	if ( $r['left'] ) {
		$msg .= ' ' . $r['left'] . ' more are waiting: click the button again.';
	}
	if ( $r['errors'] ) {
		$msg .= ' Problems: ' . implode( ' | ', $r['errors'] );
	}
	kv_flash( $r['errors'] ? 'error' : 'success', $msg );
	wp_safe_redirect( admin_url( 'admin.php?page=kv-settings' ) );
	exit;
} );

add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_options' ) || ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	$s = get_current_screen();
	if ( ! $s || ( 'dashboard' !== $s->id && false === strpos( $s->id, 'kv-' ) ) || 'kashmiri-ventures_page_kv-settings' === $s->id ) {
		return;
	}
	if ( kv_photos_missing() ) {
		echo '<div class="notice notice-warning"><p><strong>Kashmiri Ventures:</strong> the destination photos are not saved on this website yet, so they may not show. This downloads them once into your Media Library (about one minute). </p><p>';
		kv_photo_import_form();
		echo '</p></div>';
	}
} );
