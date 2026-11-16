<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------- Settings ---------- */
function kv_defaults() {
	return array(
		'logo_id'          => 0,
		'hero_id'          => 0,
		'about_img_id'     => 0,
		'hero_title'       => 'Your Dream Trip Starts Here!',
		'hero_text'        => 'Explore the beauty of Pakistan with thoughtfully planned tours and memorable experiences.',
		'hero_btn1'        => 'Explore Tours',
		'hero_btn2'        => 'Plan Your Trip',
		'search_title'     => 'Find Your Perfect Tour',
		'popular_title'    => 'Popular Tour Packages',
		'popular_text'     => 'Hand-picked routes through the mountains, valleys and lakes of Pakistan.',
		'why_title'        => 'Why Choose Kashmiri Ventures?',
		'why_text'         => 'Clear plans, honest prices and people who pick up the phone.',
		'why_items'        => "Carefully planned tours|Every route, hotel and driver is checked before the group departs.\nTransparent pricing|The package price is shown up front, with what is included and excluded.\nProfessional travel assistance|Help with choosing a route, the best season, hotels and what to pack.\nCustomer support|Message or call us before, during and after your trip.\nMemorable travel experiences|Small groups, local food and unhurried stops at the best viewpoints.",
		'departures_title' => 'Upcoming Departures',
		'reviews_title'    => 'What Our Travellers Say',
		'how_title'        => 'How Booking Works',
		'cta_title'        => 'Your Next Adventure Is Waiting.',
		'cta_text'         => 'Plan Your Trip with Kashmiri Ventures Today!',
		'about_title'      => 'About Kashmiri Ventures',
		'about_text'       => "Kashmiri Ventures (Travel with Kashmiri) runs group tours from Lahore, Faisalabad and Islamabad to Neelum Valley, Kumrat, Fairy Meadows, Hunza, Skardu, Astore and Minimarg, Swat, Kalam, Shogran and Naran Kaghan.\n\nWe also plan couple trips for married couples, private and customized tours, and hotel room booking in Murree, Naran Kaghan, Neelum Valley, Hunza, Skardu and Swat.\n\nOur trips come with a clear itinerary, a guide on tour and a team you can reach on WhatsApp before, during and after your trip.",
		'strip_items'      => "Pick and drop|From Lahore, Islamabad and Faisalabad\nFood and stay|Quality food and comfortable accommodation\nGuide and bonfire|A tour guide on every trip and a complimentary bonfire night",
		'stat_clients'     => '50000+',
		'dest_title'       => 'Our Destinations',
		'dest_text'        => 'Pick a destination to see what the trip covers and the package prices.',
		'info_title'       => 'How we travel',
		'transport_text'   => '4C, 5C Saloon or Grand Cabin (Coaster and Hiace), depending on the number of persons.',
		'food_breakfast'   => 'Omelette, Parathy, Chay, Alu bhujia / Channy',
		'food_dinner'      => '1 dish chicken',
		'food_note'        => 'The chicken dish can be chicken biryani, chicken karahi, BBQ or chicken palao.',
		'inc_common'       => "Transportation\nAccommodation\nQuality food\n1 night bonfire (complimentary)\nTour guide services\nToll taxes and challans\nFun activities",
		'exc_common'       => "Jeep charges\nLaundry\nRefreshments and extras like mineral water\nAny expenses not mentioned in the included section\nAny type of insurance\nExtra payment in case of road blockage or any natural disaster",
		'cities'           => "Lahore|Thohkar Niaz PSO pump near Daewoo terminal, or Siddique Trade Centre, Gulberg\nIslamabad|26 No. Daewoo Terminal\nFaisalabad|Kohinoor Plaza",
		'cities_notes'     => "Multan and Karachi travellers can join us from Lahore or Islamabad\nWe can arrange hotels, pick and drop on request\nTravellers near GT Road (Gujranwala, Gujrat, Sialkot, Kharian and others) can join us from GT Road\nTravellers on the motorway route (Pindi Bhattian and others) can join on the way",
		'phone'            => '+92 329 8121084',
		'phone2'           => '+92 308 6140187',
		'whatsapp'         => '923298121084',
		'email'            => 'Kashmiriventures921@gmail.com',
		'address'          => 'Office 12, Demo Plaza, Gulberg III, Lahore, Pakistan',
		'facebook'         => '',
		'instagram'        => 'https://www.instagram.com/kashmiriventures_pk',
		'youtube'          => '',
		'tiktok'           => 'https://www.tiktok.com/@kashmiri.ventures',
		'notify_email'     => 'Kashmiriventures921@gmail.com',
		'footer_text'      => 'Thoughtfully planned tours across the mountains, valleys and lakes of Pakistan.',
		'currency'         => 'PKR',
	);
}

function kv_opt( $key ) {
	$o = wp_parse_args( (array) get_option( 'kv_settings', array() ), kv_defaults() );
	return isset( $o[ $key ] ) ? $o[ $key ] : '';
}

/* ---------- Package prices (Standard / Up-Standard / Deluxe x Solo / Couple / Single) ---------- */
function kv_package_tiers() {
	return array( 'standard' => 'Standard Package', 'upstandard' => 'Up-Standard Package', 'deluxe' => 'Deluxe Package' );
}
function kv_room_types() {
	return array( 'solo' => 'Solo', 'couple' => 'Couple', 'single' => 'Single' );
}
function kv_clean_packages( $raw ) {
	$out = array();
	foreach ( kv_package_tiers() as $t => $l ) {
		$row = array();
		$any = false;
		foreach ( kv_room_types() as $r => $rl ) {
			$v        = ( isset( $raw[ $t ][ $r ] ) && is_numeric( $raw[ $t ][ $r ] ) ) ? max( 0, (int) $raw[ $t ][ $r ] ) : 0;
			$row[ $r ] = $v;
			if ( $v > 0 ) {
				$any = true;
			}
		}
		if ( $any ) {
			$out[ $t ] = $row;
		}
	}
	return $out;
}
function kv_tour_packages( $tour_id ) {
	$p = get_post_meta( $tour_id, '_kv_packages', true );
	return is_array( $p ) ? kv_clean_packages( $p ) : array();
}
function kv_packages_min( $pk ) {
	$min = 0;
	foreach ( $pk as $row ) {
		foreach ( $row as $v ) {
			if ( $v > 0 && ( 0 === $min || $v < $min ) ) {
				$min = $v;
			}
		}
	}
	return $min;
}

function kv_price_html( $tour_id ) {
	$pk = kv_tour_packages( $tour_id );
	if ( $pk ) {
		return '<small>From</small> <strong>' . kv_money( kv_packages_min( $pk ) ) . '</strong> <small>per person (Solo)</small>';
	}
	$price = (float) get_post_meta( $tour_id, '_kv_price', true );
	$label = get_post_meta( $tour_id, '_kv_price_label', true );
	$label = ( '' === $label || false === $label ) ? 'per person' : $label;
	if ( $price <= 0 ) {
		return '<strong class="price-ask">Price on request</strong>';
	}
	return '<small>From</small> <strong>' . kv_money( $price ) . '</strong> <small>' . esc_html( $label ) . '</small>';
}

function kv_price_short( $tour_id ) {
	$pk = kv_tour_packages( $tour_id );
	if ( $pk ) {
		return kv_money( kv_packages_min( $pk ) );
	}
	$price = (float) get_post_meta( $tour_id, '_kv_price', true );
	return $price > 0 ? kv_money( $price ) : 'On request';
}

function kv_lines( $text ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $l ) {
		$l = trim( $l );
		if ( '' !== $l ) {
			$out[] = $l;
		}
	}
	return $out;
}

function kv_money( $n ) {
	return esc_html( kv_opt( 'currency' ) ) . ' ' . number_format_i18n( (float) $n );
}

function kv_date( $ymd ) {
	if ( ! $ymd ) {
		return '';
	}
	return date_i18n( 'j M Y', strtotime( $ymd . ' 12:00:00' ) );
}

function kv_page_url( $slug ) {
	static $cache = array();
	if ( ! isset( $cache[ $slug ] ) ) {
		$p              = get_page_by_path( $slug );
		$cache[ $slug ] = $p ? get_permalink( $p ) : home_url( '/' . $slug . '/' );
	}
	return $cache[ $slug ];
}

/* ---------- Statuses ---------- */
function kv_statuses() {
	return array(
		'new'              => 'New Inquiry',
		'contacted'        => 'Contacted',
		'awaiting_payment' => 'Awaiting Payment',
		'payment_received' => 'Payment Received',
		'confirmed'        => 'Booking Confirmed',
		'completed'        => 'Completed',
		'cancelled'        => 'Cancelled',
	);
}

/** Statuses that reserve seats on a departure. New Inquiry, Contacted and Cancelled do not. */
function kv_held_statuses() {
	return array( 'awaiting_payment', 'payment_received', 'confirmed', 'completed' );
}

/* ---------- WhatsApp ---------- */
function kv_whatsapp_url( $msg = '' ) {
	$n   = preg_replace( '/\D+/', '', kv_opt( 'whatsapp' ) );
	$url = 'https://wa.me/' . $n;
	if ( $msg ) {
		$url .= '?text=' . rawurlencode( $msg );
	}
	return $url;
}

/* ---------- Logo ---------- */
function kv_logo_mark() {
	return '<svg class="kv-logo-mark" viewBox="0 0 64 64" width="46" height="46" aria-hidden="true" focusable="false"><circle cx="32" cy="32" r="30" fill="none" stroke="#D6AD52" stroke-width="2"/><circle cx="44" cy="20" r="5" fill="#D6AD52"/><path d="M9 45 L27 19 L37 33 L43 26 L55 45 Z" fill="#FAF9F5"/><path d="M27 19 L32 26.5 L29 25 L26 29.5 L23 25.5 Z" fill="#1F6B4D" opacity=".45"/><path d="M9 45 H55 V47 C45 52 33 49 22 53 C15 55.5 11 54 9 52 Z" fill="#2F8F68"/></svg>';
}

function kv_logo_html( $class = '' ) {
	$id = (int) kv_opt( 'logo_id' );
	if ( $id && wp_attachment_is_image( $id ) ) {
		return '<span class="kv-logo ' . esc_attr( $class ) . '">' . wp_get_attachment_image( $id, 'medium', false, array( 'class' => 'kv-logo-img', 'alt' => get_bloginfo( 'name' ) ) ) . '</span>';
	}
	$file = get_theme_file_path( 'assets/img/logo.png' );
	if ( file_exists( $file ) ) {
		return '<span class="kv-logo ' . esc_attr( $class ) . '"><img class="kv-logo-img" src="' . esc_url( get_theme_file_uri( 'assets/img/logo.png' ) ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" width="800" height="590"></span>';
	}
	return '<span class="kv-logo kv-logo-temp ' . esc_attr( $class ) . '">' . kv_logo_mark() . '<span class="kv-wordmark"><strong>Kashmiri</strong><span>Ventures</span></span></span>';
}

/* ---------- Images ---------- */
function kv_placeholder_src( $seed = 0 ) {
	$palettes = array(
		array( '#0F1D45', '#2F5AA8', '#BFD7F0' ),
		array( '#12304F', '#2F7A8A', '#D9ECF2' ),
		array( '#1B2A55', '#6A7FC0', '#F0D9C0' ),
		array( '#16324A', '#3D8A6A', '#D8EFE0' ),
		array( '#2A2340', '#7A5AA0', '#F2D6D6' ),
		array( '#10294A', '#3B7EA1', '#E8F1FA' ),
	);
	$p   = $palettes[ abs( (int) $seed ) % count( $palettes ) ];
	$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 560" preserveAspectRatio="xMidYMid slice"><defs><linearGradient id="g" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="' . $p[2] . '" stop-opacity=".9"/><stop offset=".55" stop-color="' . $p[1] . '"/><stop offset="1" stop-color="' . $p[0] . '"/></linearGradient></defs><rect width="800" height="560" fill="url(#g)"/><circle cx="600" cy="150" r="46" fill="#FAF9F5" opacity=".85"/><path d="M0 400 L140 230 L240 330 L340 190 L470 360 L560 280 L700 400 L800 340 V560 H0Z" fill="' . $p[1] . '" opacity=".55"/><path d="M0 460 L120 340 L230 430 L380 290 L520 450 L650 360 L800 470 V560 H0Z" fill="' . $p[0] . '" opacity=".85"/><path d="M380 290 L420 335 L395 325 L380 350 L360 322 Z" fill="#FAF9F5" opacity=".7"/></svg>';
	return 'data:image/svg+xml;utf8,' . rawurlencode( $svg );
}

/* ---------- Destination photos (Wikimedia Commons, freely licensed) ---------- */
function kv_photo_catalog() {
	return array(
		'hunza'  => array( "Hunza Valley, view from Eagle's Nest.jpg", 'Hunza Valley' ),
		'fairy'  => array( 'View of Nanga Parbat from Fairy Meadows National Park.jpg', 'Fairy Meadows and Nanga Parbat' ),
		'skardu' => array( 'Autumn Colors and Boat.jpg', 'Upper Kachura Lake, Skardu' ),
		'deosai' => array( 'Deosai National Park Plains.jpg', 'Deosai Plains, Skardu' ),
		'naran'  => array( 'Lake Saif ul Malook-KPK.jpg', 'Lake Saiful Muluk, Naran' ),
		'neelum' => array( 'Arang Kel - Neelum Valley.jpg', 'Arang Kel, Neelum Valley' ),
		'kumrat' => array( 'Kumrat valley, Jaaz Banda Upper Dir Kohistan, Khyber Pukhtunkhwa.jpg', 'Kumrat Valley' ),
		'kalam'  => array( 'SOMEWHERE IN USHU - Bridges in Khyber Pakhtunkhwa.jpg', 'Ushu, Kalam Valley' ),
		'astore' => array( 'View of Nanga Parbat from Rama Lake.JPG', 'Rama Lake, Astore' ),
	);
}
function kv_photo_url( $key, $width = 1200 ) {
	$c = kv_photo_catalog();
	if ( ! isset( $c[ $key ] ) ) {
		return '';
	}
	return 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode( str_replace( ' ', '_', $c[ $key ][0] ) ) . '?width=' . (int) $width;
}
function kv_photo_page( $key ) {
	$c = kv_photo_catalog();
	return isset( $c[ $key ] ) ? 'https://commons.wikimedia.org/wiki/File:' . rawurlencode( str_replace( ' ', '_', $c[ $key ][0] ) ) : '';
}
function kv_photo_key_for_text( $text ) {
	$text = strtolower( (string) $text );
	$map  = array( 'skardu' => 'skardu', 'deosai' => 'deosai', 'fairy' => 'fairy', 'hunza' => 'hunza', 'naran' => 'naran', 'kaghan' => 'naran', 'kumrat' => 'kumrat', 'astore' => 'astore', 'minimarg' => 'astore', 'neelum' => 'neelum', 'kashmir' => 'neelum', 'swat' => 'kalam', 'kalam' => 'kalam' );
	foreach ( $map as $needle => $key ) {
		if ( false !== strpos( $text, $needle ) ) {
			return $key;
		}
	}
	return '';
}

function kv_tour_img( $post_id, $size = 'kv-card', $class = '' ) {
	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail( $post_id, $size, array( 'class' => $class, 'loading' => 'lazy' ) );
	}
	$key = kv_photo_key_for_text( get_the_title( $post_id ) . ' ' . kv_tour_destination( $post_id ) );
	if ( $key ) {
		return '<img class="' . esc_attr( $class ) . '" src="' . esc_url( kv_photo_url( $key, 'kv-wide' === $size ? 1600 : 800 ) ) . '" alt="' . esc_attr( get_the_title( $post_id ) ) . '" loading="lazy" width="640" height="440">';
	}
	return '<img class="' . esc_attr( $class ) . '" src="' . esc_attr( kv_placeholder_src( $post_id ) ) . '" alt="" loading="lazy" width="640" height="440">';
}

/* ---------- Tours, departures and seats ---------- */
function kv_booked_seats( $tour_id, $date, $exclude_booking = 0 ) {
	global $wpdb;
	$t    = $wpdb->prefix . 'kv_bookings';
	$held = kv_held_statuses();
	$ph   = implode( ',', array_fill( 0, count( $held ), '%s' ) );
	$sql  = "SELECT COALESCE(SUM(travellers),0) FROM $t WHERE tour_id=%d AND departure_date=%s AND id<>%d AND status IN ($ph)";
	return (int) $wpdb->get_var( $wpdb->prepare( $sql, array_merge( array( (int) $tour_id, $date, (int) $exclude_booking ), $held ) ) );
}

function kv_get_departures( $tour_id, $upcoming_only = true ) {
	$raw = get_post_meta( $tour_id, '_kv_departures', true );
	$out = array();
	if ( ! is_array( $raw ) ) {
		return $out;
	}
	$today = current_time( 'Y-m-d' );
	foreach ( $raw as $d ) {
		if ( empty( $d['date'] ) ) {
			continue;
		}
		if ( $upcoming_only && $d['date'] < $today ) {
			continue;
		}
		$seats  = max( 0, (int) $d['seats'] );
		$booked = kv_booked_seats( $tour_id, $d['date'] );
		$out[]  = array(
			'date'      => $d['date'],
			'seats'     => $seats,
			'booked'    => $booked,
			'remaining' => max( 0, $seats - $booked ),
		);
	}
	usort( $out, function ( $a, $b ) {
		return strcmp( $a['date'], $b['date'] );
	} );
	return $out;
}

function kv_next_departure( $tour_id ) {
	$deps = kv_get_departures( $tour_id );
	foreach ( $deps as $d ) {
		if ( $d['remaining'] > 0 ) {
			return $d;
		}
	}
	return $deps ? $deps[0] : null;
}

function kv_seat_status( $remaining ) {
	if ( $remaining <= 0 ) {
		return array( 'full', 'Fully booked' );
	}
	if ( $remaining <= 4 ) {
		return array( 'few', 'Few seats left' );
	}
	return array( 'open', 'Booking open' );
}

function kv_tour_destination( $tour_id ) {
	$t = get_the_terms( $tour_id, 'kv_destination' );
	return ( $t && ! is_wp_error( $t ) ) ? $t[0]->name : '';
}

function kv_book_url( $tour_id = 0, $date = '' ) {
	$args = array();
	if ( $tour_id ) {
		$args['tour'] = (int) $tour_id;
	}
	if ( $date ) {
		$args['date'] = $date;
	}
	return add_query_arg( $args, kv_page_url( 'book' ) );
}

function kv_search_tours( $args = array() ) {
	$a = wp_parse_args( $args, array( 'dest' => '', 'date' => '', 'travellers' => 0, 'type' => '', 'limit' => -1, 'featured' => false ) );
	$q = array(
		'post_type'      => 'kv_tour',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	);
	$tax = array();
	if ( $a['dest'] ) {
		$tax[] = array( 'taxonomy' => 'kv_destination', 'field' => 'slug', 'terms' => sanitize_title( $a['dest'] ) );
	}
	if ( $a['type'] ) {
		$tax[] = array( 'taxonomy' => 'kv_tour_type', 'field' => 'slug', 'terms' => sanitize_title( $a['type'] ) );
	}
	if ( $tax ) {
		$q['tax_query'] = $tax;
	}
	if ( $a['featured'] ) {
		$q['meta_query'] = array( array( 'key' => '_kv_featured', 'value' => '1' ) );
	}
	$posts = get_posts( $q );
	$date  = preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $a['date'] ) ? $a['date'] : '';
	$trav  = (int) $a['travellers'];
	if ( $date || $trav ) {
		$posts = array_filter( $posts, function ( $p ) use ( $date, $trav ) {
			foreach ( kv_get_departures( $p->ID ) as $d ) {
				if ( $date && $d['date'] < $date ) {
					continue;
				}
				if ( $trav && $d['remaining'] < $trav ) {
					continue;
				}
				return true;
			}
			return false;
		} );
	}
	$posts = array_values( $posts );
	if ( $a['limit'] > 0 ) {
		$posts = array_slice( $posts, 0, (int) $a['limit'] );
	}
	return $posts;
}

/* ---------- Bookings ---------- */
function kv_get_booking( $id ) {
	global $wpdb;
	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}kv_bookings WHERE id=%d", (int) $id ) );
}
function kv_get_booking_by_ref( $ref ) {
	global $wpdb;
	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}kv_bookings WHERE reference=%s", $ref ) );
}
function kv_log( $booking_id, $text ) {
	global $wpdb;
	$u = wp_get_current_user();
	$wpdb->insert(
		$wpdb->prefix . 'kv_booking_log',
		array(
			'booking_id' => (int) $booking_id,
			'actor'      => ( $u && $u->ID ) ? $u->display_name : 'Customer',
			'entry'      => $text,
			'created_at' => current_time( 'mysql' ),
		),
		array( '%d', '%s', '%s', '%s' )
	);
}
