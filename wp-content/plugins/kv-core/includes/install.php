<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function kv_activate() {
	kv_register_post_types();
	kv_create_tables();
	kv_seed_terms();
	kv_seed_destinations();
	kv_apply_client_contact();
	kv_create_pages();
	if ( ! get_option( 'kv_real_imported' ) ) {
		kv_import_real_content();
	}
	kv_seed_packages_and_sections();
	flush_rewrite_rules();
}

add_action( 'init', 'kv_maybe_upgrade', 20 );
function kv_maybe_upgrade() {
	if ( get_option( 'kv_db_version' ) === KV_VERSION ) {
		return;
	}
	kv_create_tables();
	kv_seed_destinations();
	kv_apply_client_contact();
	if ( ! get_option( 'kv_real_imported' ) ) {
		kv_import_real_content();
	}
	kv_seed_packages_and_sections();
	kv_create_pages();
}

/* Destinations given by the client. Existing destinations are kept. */
function kv_seed_destinations() {
	$names = array( 'Skardu', 'Hunza', 'Fairy Meadows', 'Naran Kaghan', 'Shogran', 'Kashmir', 'Swat Kalam', 'Kumrat Valley', 'Astore Minimarg' );
	$old   = term_exists( 'Swat', 'kv_destination' );
	if ( $old && ! term_exists( 'Swat Kalam', 'kv_destination' ) ) {
		$id = is_array( $old ) ? $old['term_id'] : $old;
		wp_update_term( (int) $id, 'kv_destination', array( 'name' => 'Swat Kalam', 'slug' => 'swat-kalam' ) );
	}
	foreach ( $names as $n ) {
		if ( ! term_exists( $n, 'kv_destination' ) ) {
			wp_insert_term( $n, 'kv_destination' );
		}
	}
}

/* Package prices for the three 3-day tours and the homepage destination sections. Runs once. */
function kv_seed_packages_and_sections() {
	$grid = array(
		'standard'   => array( 'solo' => 15500, 'couple' => 38000, 'single' => 25000 ),
		'upstandard' => array( 'solo' => 19500, 'couple' => 49000, 'single' => 29000 ),
		'deluxe'     => array( 'solo' => 37000, 'couple' => 90000, 'single' => 70000 ),
	);
	$find = function ( $title ) {
		$p = get_posts( array( 'post_type' => 'kv_tour', 'title' => $title, 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) );
		return $p ? (int) $p[0] : 0;
	};
	$neelum = '3 Days Kashmir and Ratti Gali Lake';
	$naran  = '3 Days Naran, Kaghan and Babusar Pass';
	$swat   = '3 Days Swat, Kalam and Malam Jabba';
	if ( ! get_option( 'kv_packages_seeded' ) ) {
		foreach ( array( $neelum, $naran, $swat ) as $t ) {
			$id = $find( $t );
			if ( $id && ! get_post_meta( $id, '_kv_packages', true ) ) {
				update_post_meta( $id, '_kv_packages', $grid );
			}
		}
		update_option( 'kv_packages_seeded', '1' );
	}
	$s = get_option( 'kv_settings', array() );
	$s = is_array( $s ) ? $s : array();
	if ( ! isset( $s['dest_sections'] ) ) {
		$defs = array(
			array( 'Neelum Valley', 'Dhani and Kutton waterfalls, the LOC viewpoint, Keran and the hike to Ratti Gali Lake.', $neelum, $grid ),
			array( 'Naran Kaghan', 'Naran Bazaar, Battakundi, Lulusar Lake and Babusar Pass with a bonfire and BBQ night.', $naran, $grid ),
			array( 'Swat and Kalam', 'Malam Jabba, Kalam Valley, Ushu Forest, Palogha Valley and Mahodand Lake.', $swat, $grid ),
			array( 'Hunza Valley', 'Rakaposhi viewpoint, Altit and Baltit forts, Attabad Lake, Passu Cones and Khunjerab.', '5 Days Hunza Valley and Naran Valley', array() ),
			array( 'Skardu', 'Shangrila and Upper Kachura lakes, Shigar Fort, the cold desert and Basho Valley.', '6 Days Skardu Valley', array() ),
			array( 'Fairy Meadows', 'Jeep safari to Tattu Village, the trek to Fairy Meadows and the view of Nanga Parbat.', '5 Days Fairy Meadows and Naran Valley', array() ),
			array( 'Kumrat Valley', 'Kala Chashma, Kumrat waterfall, Jahaz Bhanda and the hike to Katora Lake.', '4 Days Kumrat Valley, Katora Lake and Jahaz Bhanda', array() ),
			array( 'Astore and Minimarg', 'Camping at Rama Meadows, Rama Lake, the Burzil Pass drive and Minimarg.', '6 Days Astore Valley and Minimarg', array() ),
		);
		$rows = array();
		foreach ( $defs as $d ) {
			$rows[] = array( 'show' => 1, 'name' => $d[0], 'heading' => $d[0], 'text' => $d[1], 'image_id' => 0, 'tour_id' => $find( $d[2] ), 'prices' => $d[3] );
		}
		$s['dest_sections'] = $rows;
		update_option( 'kv_settings', $s );
	}
}

/* Client contact details. Only replaces values that are still empty or the old sample values. */
function kv_apply_client_contact() {
	$saved = get_option( 'kv_settings', array() );
	$saved = is_array( $saved ) ? $saved : array();
	$set   = function ( $key, $new, $olds ) use ( &$saved ) {
		if ( ! isset( $saved[ $key ] ) || in_array( $saved[ $key ], $olds, true ) ) {
			$saved[ $key ] = $new;
		}
	};
	$set( 'phone', '+92 329 8121084', array( '', '+92 300 1234567' ) );
	$set( 'phone2', '+92 308 6140187', array( '' ) );
	$set( 'email', 'Kashmiriventures921@gmail.com', array( '', 'info@kashmiriventures.example' ) );
	$set( 'notify_email', 'Kashmiriventures921@gmail.com', array( '' ) );
	$set( 'whatsapp', '923298121084', array( '', '923001234567' ) );
	$set( 'instagram', 'https://www.instagram.com/kashmiriventures_pk', array( '', 'https://www.instagram.com/' ) );
	$set( 'tiktok', 'https://www.tiktok.com/@kashmiri.ventures', array( '' ) );
	$set( 'facebook', '', array( 'https://www.facebook.com/' ) );
	$set( 'youtube', '', array( 'https://www.youtube.com/' ) );
	update_option( 'kv_settings', $saved );
}

function kv_create_tables() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$c = $wpdb->get_charset_collate();
	$p = $wpdb->prefix;

	dbDelta( "CREATE TABLE {$p}kv_bookings (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  reference varchar(30) NOT NULL,
  tour_id bigint(20) unsigned NOT NULL DEFAULT 0,
  tour_title varchar(255) NOT NULL DEFAULT '',
  departure_date date DEFAULT NULL,
  customer_name varchar(150) NOT NULL DEFAULT '',
  email varchar(150) NOT NULL DEFAULT '',
  phone varchar(40) NOT NULL DEFAULT '',
  whatsapp varchar(40) NOT NULL DEFAULT '',
  adults smallint(5) unsigned NOT NULL DEFAULT 1,
  children smallint(5) unsigned NOT NULL DEFAULT 0,
  travellers smallint(5) unsigned NOT NULL DEFAULT 1,
  special_requests text,
  additional_info text,
  total_amount decimal(12,2) NOT NULL DEFAULT 0,
  paid_amount decimal(12,2) NOT NULL DEFAULT 0,
  payment_status varchar(20) NOT NULL DEFAULT 'unpaid',
  status varchar(30) NOT NULL DEFAULT 'new',
  admin_notes text,
  package_tier varchar(20) NOT NULL DEFAULT '',
  room_type varchar(20) NOT NULL DEFAULT '',
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY reference (reference),
  KEY tour_date (tour_id,departure_date),
  KEY status (status),
  KEY email (email)
) $c;" );

	dbDelta( "CREATE TABLE {$p}kv_payments (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  booking_id bigint(20) unsigned NOT NULL,
  amount decimal(12,2) NOT NULL DEFAULT 0,
  paid_on date DEFAULT NULL,
  method varchar(60) NOT NULL DEFAULT '',
  txn_ref varchar(120) NOT NULL DEFAULT '',
  status varchar(20) NOT NULL DEFAULT 'pending',
  notes text,
  created_at datetime NOT NULL,
  PRIMARY KEY  (id),
  KEY booking_id (booking_id)
) $c;" );

	dbDelta( "CREATE TABLE {$p}kv_booking_log (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  booking_id bigint(20) unsigned NOT NULL,
  actor varchar(120) NOT NULL DEFAULT '',
  entry text,
  created_at datetime NOT NULL,
  PRIMARY KEY  (id),
  KEY booking_id (booking_id)
) $c;" );

	update_option( 'kv_db_version', KV_VERSION );
}

function kv_seed_terms() {
	foreach ( array( 'Group Tour', 'Family Tour', 'Private Tour', 'Customized Tour' ) as $name ) {
		if ( ! term_exists( $name, 'kv_tour_type' ) ) {
			wp_insert_term( $name, 'kv_tour_type' );
		}
	}
}

function kv_policy_text( $title ) {
	$note = '<p><em>Draft text. Please review and edit this page with the business owner before the website goes live.</em></p>';
	$map  = array(
		'Booking Policies'        => '<h3>Making a booking</h3><p>A booking request is submitted through the website or WhatsApp. It becomes a confirmed booking only after our team confirms the details and receives the agreed advance payment.</p><h3>Seats</h3><p>Seats are reserved once a booking is marked Awaiting Payment or later. Unpaid reservations may be released if payment is not received by the agreed date.</p><h3>Changes</h3><p>Please contact us as early as possible to change dates, travellers or hotels. Changes depend on availability.</p>',
		'Cancellation Policy'     => '<h3>Cancelling a booking</h3><p>Please tell us in writing (email or WhatsApp) as soon as you decide to cancel.</p><p>Cancellation charges depend on how close to departure the cancellation is made. Add the exact percentages and number of days here.</p>',
		'Refund Policy'           => '<h3>Refunds</h3><p>Eligible refunds are returned using the same payment method where possible. Add the refund timeline and any non-refundable costs (for example flights or hotel deposits) here.</p>',
		'Privacy Policy'          => '<h3>Information we collect</h3><p>We collect the name, email, phone number and trip details you send us in order to handle your booking and contact you about your trip.</p><h3>How we use it</h3><p>We use it only to plan and manage your tour and to answer your questions. We do not sell your information.</p><h3>Contact</h3><p>Write to us at the email address shown on the Contact page to ask about or delete your information.</p>',
		'Terms and Conditions'    => '<h3>Terms of service</h3><p>By booking a tour you agree to follow the itinerary, safety guidance of the tour guide and local laws and customs. Itineraries may change because of weather, road closures or security conditions. Add the full terms here.</p>',
	);
	return $note . ( isset( $map[ $title ] ) ? $map[ $title ] : '' );
}

function kv_create_pages() {
	$pages = array(
		'home'                  => array( 'Home', '' ),
		'about'                 => array( 'About Us', '' ),
		'contact'               => array( 'Contact Us', '' ),
		'faqs'                  => array( 'FAQs', '' ),
		'customer-reviews'      => array( 'Customer Reviews', '' ),
		'gallery'               => array( 'Photo Gallery', '' ),
		'book'                  => array( 'Book Your Tour', '' ),
		'booking-confirmation'  => array( 'Booking Received', '' ),
		'booking-policy'        => array( 'Booking Policies', kv_policy_text( 'Booking Policies' ) ),
		'cancellation-policy'   => array( 'Cancellation Policy', kv_policy_text( 'Cancellation Policy' ) ),
		'refund-policy'         => array( 'Refund Policy', kv_policy_text( 'Refund Policy' ) ),
		'privacy-policy'        => array( 'Privacy Policy', kv_policy_text( 'Privacy Policy' ) ),
		'terms-and-conditions'  => array( 'Terms and Conditions', kv_policy_text( 'Terms and Conditions' ) ),
		'photo-credits'         => array( 'Photo Credits', '' ),
	);
	foreach ( $pages as $slug => $p ) {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $existing ) {
			if ( 'draft' === $existing->post_status && 'privacy-policy' === $slug ) {
				wp_update_post( array( 'ID' => $existing->ID, 'post_status' => 'publish', 'post_content' => $p[1] ) );
			}
			continue;
		}
		$id = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $p[0],
			'post_name'    => $slug,
			'post_content' => $p[1],
		) );
		if ( 'home' === $slug && $id && ! is_wp_error( $id ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $id );
		}
	}
}
