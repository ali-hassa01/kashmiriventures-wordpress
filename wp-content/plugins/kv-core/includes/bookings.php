<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ============ Public booking form ============ */
add_action( 'admin_post_nopriv_kv_submit_booking', 'kv_handle_booking' );
add_action( 'admin_post_kv_submit_booking', 'kv_handle_booking' );

function kv_booking_error_message( $code ) {
	$m = array(
		'tour'       => 'Please choose a tour.',
		'name'       => 'Please enter your full name.',
		'email'      => 'Please enter a valid email address.',
		'phone'      => 'Please enter a valid phone number.',
		'travellers' => 'Please enter at least one adult traveller.',
		'date'       => 'Please choose one of the available departure dates.',
		'seats'      => 'There are not enough seats left on that date for your group. Please choose another date or fewer travellers, or contact us on WhatsApp.',
		'rate'       => 'Too many requests from your connection. Please wait a while and try again, or contact us on WhatsApp.',
		'package'    => 'Please choose a package and room type that is available for this tour.',
		'couple'     => 'A Couple room is for 2 persons. Please enter an even number of travellers, or choose Solo or Single.',
		'db'         => 'We could not save your booking. Please try again or contact us on WhatsApp.',
	);
	return isset( $m[ $code ] ) ? $m[ $code ] : 'Something went wrong. Please try again.';
}

function kv_booking_fail( $code, $data ) {
	$key = strtolower( wp_generate_password( 12, false, false ) );
	set_transient( 'kv_form_' . $key, $data, 10 * MINUTE_IN_SECONDS );
	wp_safe_redirect( add_query_arg( array( 'kv_err' => $code, 'kv_form' => $key ), kv_page_url( 'book' ) ) . '#booking-form' );
	exit;
}

function kv_unique_reference() {
	for ( $i = 0; $i < 6; $i++ ) {
		$rnd = strtoupper( substr( str_replace( array( '0', 'O', '1', 'I', 'l', 'o' ), '', wp_generate_password( 20, false, false ) ), 0, 5 ) );
		$ref = 'KV-' . current_time( 'ymd' ) . '-' . $rnd;
		if ( 5 === strlen( $rnd ) && ! kv_get_booking_by_ref( $ref ) ) {
			return $ref;
		}
	}
	return 'KV-' . current_time( 'ymdHis' ) . '-' . wp_rand( 100, 999 );
}

function kv_handle_booking() {
	global $wpdb;
	$nonce = isset( $_POST['_kvnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_kvnonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'kv_booking' ) ) {
		wp_die( 'Your session expired. Please go back, refresh the page and send the form again.', 'Session expired', array( 'response' => 403, 'back_link' => true ) );
	}
	if ( ! empty( $_POST['kv_website'] ) ) { // honeypot
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
	$g   = function ( $k ) {
		return isset( $_POST[ $k ] ) ? wp_unslash( $_POST[ $k ] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	};
	$raw = array(
		'tour_id'        => absint( $g( 'tour_id' ) ),
		'departure_date' => sanitize_text_field( $g( 'departure_date' ) ),
		'preferred_date' => sanitize_text_field( $g( 'preferred_date' ) ),
		'name'           => sanitize_text_field( $g( 'name' ) ),
		'email'          => sanitize_email( $g( 'email' ) ),
		'phone'          => sanitize_text_field( $g( 'phone' ) ),
		'whatsapp'       => sanitize_text_field( $g( 'whatsapp' ) ),
		'adults'         => (int) $g( 'adults' ),
		'children'       => (int) $g( 'children' ),
		'special'        => mb_substr( sanitize_textarea_field( $g( 'special' ) ), 0, 2000 ),
		'info'           => mb_substr( sanitize_textarea_field( $g( 'info' ) ), 0, 2000 ),
		'package_tier'   => sanitize_key( $g( 'package_tier' ) ),
		'room_type'      => sanitize_key( $g( 'room_type' ) ),
	);

	$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$rkey = 'kv_rl_' . md5( $ip );
	$hits = (int) get_transient( $rkey );
	if ( $hits >= 6 ) {
		kv_booking_fail( 'rate', $raw );
	}

	$tour = $raw['tour_id'] ? get_post( $raw['tour_id'] ) : null;
	if ( ! $tour || 'kv_tour' !== $tour->post_type || 'publish' !== $tour->post_status ) {
		kv_booking_fail( 'tour', $raw );
	}
	if ( mb_strlen( $raw['name'] ) < 2 ) {
		kv_booking_fail( 'name', $raw );
	}
	if ( ! is_email( $raw['email'] ) ) {
		kv_booking_fail( 'email', $raw );
	}
	if ( strlen( preg_replace( '/\D+/', '', $raw['phone'] ) ) < 7 ) {
		kv_booking_fail( 'phone', $raw );
	}
	if ( $raw['adults'] < 1 || $raw['adults'] > 50 || $raw['children'] < 0 || $raw['children'] > 50 ) {
		kv_booking_fail( 'travellers', $raw );
	}
	$travellers = $raw['adults'] + $raw['children'];

	$date = null;
	$deps = kv_get_departures( $tour->ID );
	if ( $deps ) {
		foreach ( $deps as $d ) {
			if ( $d['date'] === $raw['departure_date'] ) {
				$date = $d;
			}
		}
		if ( ! $date ) {
			kv_booking_fail( 'date', $raw );
		}
		if ( $travellers > $date['remaining'] ) {
			kv_booking_fail( 'seats', $raw );
		}
		$date = $date['date'];
	} elseif ( '' !== $raw['preferred_date'] ) {
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw['preferred_date'] ) || $raw['preferred_date'] < current_time( 'Y-m-d' ) ) {
			kv_booking_fail( 'date', $raw );
		}
		$date = $raw['preferred_date'];
	}

	$pk   = kv_tour_packages( $tour->ID );
	$tier = '';
	$room = '';
	if ( $pk ) {
		$tier = $raw['package_tier'];
		$room = $raw['room_type'];
		if ( ! isset( $pk[ $tier ] ) || ! isset( kv_room_types()[ $room ] ) || (int) $pk[ $tier ][ $room ] <= 0 ) {
			kv_booking_fail( 'package', $raw );
		}
		if ( 'couple' === $room && 0 !== $travellers % 2 ) {
			kv_booking_fail( 'couple', $raw );
		}
		$total = (int) $pk[ $tier ][ $room ] * ( 'couple' === $room ? $travellers / 2 : $travellers );
	} else {
		$price = (float) get_post_meta( $tour->ID, '_kv_price', true );
		$child = get_post_meta( $tour->ID, '_kv_child_price', true );
		$child = ( '' === $child || null === $child ) ? $price : (float) $child;
		$total = ( $raw['adults'] * $price ) + ( $raw['children'] * $child );
	}

	$now = current_time( 'mysql' );
	$ref = kv_unique_reference();
	$ok  = $wpdb->insert(
		$wpdb->prefix . 'kv_bookings',
		array(
			'reference'        => $ref,
			'tour_id'          => $tour->ID,
			'tour_title'       => $tour->post_title,
			'departure_date'   => $date,
			'customer_name'    => $raw['name'],
			'email'            => $raw['email'],
			'phone'            => $raw['phone'],
			'whatsapp'         => '' !== $raw['whatsapp'] ? $raw['whatsapp'] : $raw['phone'],
			'adults'           => $raw['adults'],
			'children'         => $raw['children'],
			'travellers'       => $travellers,
			'special_requests' => $raw['special'],
			'additional_info'  => $raw['info'],
			'total_amount'     => $total,
			'paid_amount'      => 0,
			'payment_status'   => 'unpaid',
			'status'           => 'new',
			'admin_notes'      => '',
			'package_tier'     => $tier,
			'room_type'        => $room,
			'created_at'       => $now,
			'updated_at'       => $now,
		),
		array( '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
	);
	if ( ! $ok ) {
		kv_booking_fail( 'db', $raw );
	}
	$id = (int) $wpdb->insert_id;
	kv_log( $id, 'Booking request submitted from the website.' );
	set_transient( $rkey, $hits + 1, HOUR_IN_SECONDS );
	kv_notify_booking( $id );

	wp_safe_redirect( add_query_arg( 'ref', rawurlencode( $ref ), kv_page_url( 'booking-confirmation' ) ) );
	exit;
}

function kv_notify_booking( $id ) {
	$b = kv_get_booking( $id );
	if ( ! $b ) {
		return;
	}
	$admin = kv_opt( 'notify_email' );
	if ( ! is_email( $admin ) ) {
		$admin = get_option( 'admin_email' );
	}
	$details = "Reference: {$b->reference}\nTour: {$b->tour_title}\nDeparture: " . ( $b->departure_date ? kv_date( $b->departure_date ) : 'Not set' ) . "\nTravellers: {$b->travellers} ({$b->adults} adults, {$b->children} children)\nEstimated package amount: " . ( (float) $b->total_amount > 0 ? wp_strip_all_tags( kv_money( $b->total_amount ) ) : 'To be confirmed by our team' );

	if ( ! empty( $b->package_tier ) ) {
		$tiers   = kv_package_tiers();
		$rooms   = kv_room_types();
		$details .= "\nPackage: " . ( isset( $tiers[ $b->package_tier ] ) ? $tiers[ $b->package_tier ] : $b->package_tier ) . ' / ' . ( isset( $rooms[ $b->room_type ] ) ? $rooms[ $b->room_type ] : $b->room_type );
	}

	$body = "A new booking request was submitted.\n\n$details\n\nName: {$b->customer_name}\nEmail: {$b->email}\nPhone: {$b->phone}\nWhatsApp: {$b->whatsapp}\n\nSpecial requests: {$b->special_requests}\nAdditional information: {$b->additional_info}\n\nOpen it in wp-admin:\n" . admin_url( 'admin.php?page=kv-bookings&booking=' . $b->id );
	wp_mail( $admin, '[' . get_bloginfo( 'name' ) . '] New booking request ' . $b->reference, $body, array( 'Reply-To: ' . $b->email ) );

	$ack = "Dear {$b->customer_name},\n\nThank you for your booking request with " . get_bloginfo( 'name' ) . ". This message is an acknowledgement. Your booking is not confirmed until our team contacts you and the details and payment are agreed.\n\n$details\n\nIf you have questions, reply to this email or contact us on WhatsApp: " . kv_opt( 'phone' ) . "\n\n" . get_bloginfo( 'name' );
	wp_mail( $b->email, 'We received your booking request ' . $b->reference, $ack );
}

/* ============ Status and payments (admin) ============ */
function kv_set_booking_status( $id, $new ) {
	global $wpdb;
	$b = kv_get_booking( $id );
	if ( ! $b ) {
		return new WP_Error( 'nf', 'Booking not found.' );
	}
	$labels = kv_statuses();
	if ( ! isset( $labels[ $new ] ) ) {
		return new WP_Error( 'bad', 'Unknown status.' );
	}
	if ( $b->status === $new ) {
		return true;
	}
	$held    = kv_held_statuses();
	$needs   = in_array( $new, $held, true ) && ! in_array( $b->status, $held, true );
	$lock    = 0;
	$lockkey = 'kv_seats_' . (int) $b->tour_id;
	if ( $needs && $b->departure_date && $b->tour_id ) {
		$lock = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 5)', $lockkey ) );
		if ( ! $lock ) {
			return new WP_Error( 'lock', 'Another booking is being updated right now. Please try again in a few seconds.' );
		}
		$seats = null;
		foreach ( kv_get_departures( $b->tour_id, false ) as $d ) {
			if ( $d['date'] === $b->departure_date ) {
				$seats = $d['seats'];
			}
		}
		if ( null !== $seats ) {
			$booked = kv_booked_seats( $b->tour_id, $b->departure_date, $b->id );
			if ( $booked + (int) $b->travellers > $seats ) {
				$wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lockkey ) );
				return new WP_Error( 'seats', sprintf( 'Not enough seats. This departure has %d seats, %d are already held by other bookings and this booking needs %d. Increase the seats on the tour or choose another status.', $seats, $booked, (int) $b->travellers ) );
			}
		}
	}
	$wpdb->update(
		$wpdb->prefix . 'kv_bookings',
		array( 'status' => $new, 'updated_at' => current_time( 'mysql' ) ),
		array( 'id' => (int) $id ),
		array( '%s', '%s' ),
		array( '%d' )
	);
	if ( $lock ) {
		$wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lockkey ) );
	}
	kv_log( $id, sprintf( 'Status changed from %s to %s.', $labels[ $b->status ], $labels[ $new ] ) );
	return true;
}

function kv_recalc_payment( $booking_id ) {
	global $wpdb;
	$b = kv_get_booking( $booking_id );
	if ( ! $b ) {
		return;
	}
	$paid = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(amount),0) FROM {$wpdb->prefix}kv_payments WHERE booking_id=%d AND status='verified'", (int) $booking_id ) );
	if ( $paid <= 0 ) {
		$ps = 'unpaid';
	} elseif ( (float) $b->total_amount > 0 && $paid >= (float) $b->total_amount ) {
		$ps = 'paid';
	} else {
		$ps = 'partial';
	}
	$wpdb->update(
		$wpdb->prefix . 'kv_bookings',
		array( 'paid_amount' => $paid, 'payment_status' => $ps, 'updated_at' => current_time( 'mysql' ) ),
		array( 'id' => (int) $booking_id ),
		array( '%f', '%s', '%s' ),
		array( '%d' )
	);
}

function kv_flash( $type, $text ) {
	set_transient( 'kv_notice_' . get_current_user_id(), array( 'type' => $type, 'text' => $text ), 60 );
}

function kv_back_to_booking( $id ) {
	wp_safe_redirect( admin_url( 'admin.php?page=kv-bookings&booking=' . (int) $id ) );
	exit;
}

add_action( 'admin_post_kv_update_booking', 'kv_admin_update_booking' );
function kv_admin_update_booking() {
	global $wpdb;
	$id = isset( $_POST['booking_id'] ) ? absint( $_POST['booking_id'] ) : 0;
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You are not allowed to do this.' );
	}
	check_admin_referer( 'kv_update_booking_' . $id );
	$b = kv_get_booking( $id );
	if ( ! $b ) {
		wp_die( 'Booking not found.' );
	}
	$notes  = isset( $_POST['admin_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['admin_notes'] ) ) : $b->admin_notes;
	$total  = isset( $_POST['total_amount'] ) ? max( 0, (float) $_POST['total_amount'] ) : (float) $b->total_amount;
	$status = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : $b->status;

	$wpdb->update(
		$wpdb->prefix . 'kv_bookings',
		array( 'admin_notes' => $notes, 'total_amount' => $total, 'updated_at' => current_time( 'mysql' ) ),
		array( 'id' => $id ),
		array( '%s', '%f', '%s' ),
		array( '%d' )
	);
	if ( $notes !== (string) $b->admin_notes ) {
		kv_log( $id, 'Admin notes updated.' );
	}
	if ( abs( $total - (float) $b->total_amount ) > 0.001 ) {
		kv_log( $id, sprintf( 'Total amount changed from %s to %s.', (float) $b->total_amount, $total ) );
	}
	kv_recalc_payment( $id );
	$r = kv_set_booking_status( $id, $status );
	if ( is_wp_error( $r ) ) {
		kv_flash( 'error', 'Saved notes and amount, but the status was not changed. ' . $r->get_error_message() );
	} else {
		kv_flash( 'success', 'Booking updated.' );
	}
	kv_back_to_booking( $id );
}

add_action( 'admin_post_kv_add_payment', 'kv_admin_add_payment' );
function kv_admin_add_payment() {
	global $wpdb;
	$id = isset( $_POST['booking_id'] ) ? absint( $_POST['booking_id'] ) : 0;
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You are not allowed to do this.' );
	}
	check_admin_referer( 'kv_add_payment_' . $id );
	if ( ! kv_get_booking( $id ) ) {
		wp_die( 'Booking not found.' );
	}
	$amount = isset( $_POST['amount'] ) ? (float) $_POST['amount'] : 0;
	$date   = isset( $_POST['paid_on'] ) ? sanitize_text_field( wp_unslash( $_POST['paid_on'] ) ) : '';
	$date   = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ? $date : current_time( 'Y-m-d' );
	$status = ( isset( $_POST['pay_status'] ) && 'verified' === $_POST['pay_status'] ) ? 'verified' : 'pending';
	if ( $amount <= 0 ) {
		kv_flash( 'error', 'Enter a payment amount greater than zero.' );
		kv_back_to_booking( $id );
	}
	$wpdb->insert(
		$wpdb->prefix . 'kv_payments',
		array(
			'booking_id' => $id,
			'amount'     => $amount,
			'paid_on'    => $date,
			'method'     => isset( $_POST['method'] ) ? sanitize_text_field( wp_unslash( $_POST['method'] ) ) : '',
			'txn_ref'    => isset( $_POST['txn_ref'] ) ? sanitize_text_field( wp_unslash( $_POST['txn_ref'] ) ) : '',
			'status'     => $status,
			'notes'      => isset( $_POST['pay_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['pay_notes'] ) ) : '',
			'created_at' => current_time( 'mysql' ),
		),
		array( '%d', '%f', '%s', '%s', '%s', '%s', '%s', '%s' )
	);
	kv_log( $id, sprintf( 'Payment record added: %s (%s).', $amount, $status ) );
	kv_recalc_payment( $id );
	kv_flash( 'success', 'Payment record saved. Only payments marked Verified count towards the paid amount.' );
	kv_back_to_booking( $id );
}

add_action( 'admin_post_kv_payment_status', 'kv_admin_payment_status' );
function kv_admin_payment_status() {
	global $wpdb;
	$pid = isset( $_POST['payment_id'] ) ? absint( $_POST['payment_id'] ) : 0;
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You are not allowed to do this.' );
	}
	check_admin_referer( 'kv_payment_status_' . $pid );
	$p = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}kv_payments WHERE id=%d", $pid ) );
	if ( ! $p ) {
		wp_die( 'Payment not found.' );
	}
	$new = ( isset( $_POST['new_status'] ) && 'verified' === $_POST['new_status'] ) ? 'verified' : 'pending';
	$wpdb->update( $wpdb->prefix . 'kv_payments', array( 'status' => $new ), array( 'id' => $pid ), array( '%s' ), array( '%d' ) );
	kv_log( $p->booking_id, sprintf( 'Payment of %s marked %s.', $p->amount, $new ) );
	kv_recalc_payment( $p->booking_id );
	kv_flash( 'success', 'Payment updated.' );
	kv_back_to_booking( $p->booking_id );
}

/* ============ Contact form ============ */
add_action( 'admin_post_nopriv_kv_contact', 'kv_handle_contact' );
add_action( 'admin_post_kv_contact', 'kv_handle_contact' );
function kv_handle_contact() {
	$nonce = isset( $_POST['_kvnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_kvnonce'] ) ) : '';
	$back  = kv_page_url( 'contact' );
	if ( ! wp_verify_nonce( $nonce, 'kv_contact' ) ) {
		wp_safe_redirect( add_query_arg( 'sent', '0', $back ) );
		exit;
	}
	if ( ! empty( $_POST['kv_website'] ) ) {
		wp_safe_redirect( $back );
		exit;
	}
	$name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$msg   = isset( $_POST['message'] ) ? mb_substr( sanitize_textarea_field( wp_unslash( $_POST['message'] ) ), 0, 3000 ) : '';
	if ( mb_strlen( $name ) < 2 || ! is_email( $email ) || mb_strlen( $msg ) < 5 ) {
		wp_safe_redirect( add_query_arg( 'sent', '0', $back ) );
		exit;
	}
	$admin = kv_opt( 'notify_email' );
	if ( ! is_email( $admin ) ) {
		$admin = get_option( 'admin_email' );
	}
	$sent = wp_mail( $admin, '[' . get_bloginfo( 'name' ) . '] Website message from ' . $name, "Name: $name\nEmail: $email\nPhone: $phone\n\n$msg", array( 'Reply-To: ' . $email ) );
	wp_safe_redirect( add_query_arg( 'sent', $sent ? '1' : '0', $back ) );
	exit;
}
