<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ============ Menu and assets ============ */
add_action( 'admin_menu', 'kv_admin_menu', 5 );
function kv_admin_menu() {
	add_menu_page( 'Kashmiri Ventures', 'Kashmiri Ventures', 'manage_options', 'kv-dashboard', 'kv_render_dashboard', 'dashicons-palmtree', 25 );
	add_submenu_page( 'kv-dashboard', 'Dashboard', 'Dashboard', 'manage_options', 'kv-dashboard', 'kv_render_dashboard' );
	add_submenu_page( 'kv-dashboard', 'Bookings', 'Bookings', 'manage_options', 'kv-bookings', 'kv_render_bookings' );
	add_submenu_page( 'kv-dashboard', 'Customers', 'Customers', 'manage_options', 'kv-customers', 'kv_render_customers' );
}
add_action( 'admin_menu', function () {
	add_submenu_page( 'kv-dashboard', 'Destinations', 'Destinations', 'manage_options', 'edit-tags.php?taxonomy=kv_destination&post_type=kv_tour' );
	add_submenu_page( 'kv-dashboard', 'Tour Types', 'Tour Types', 'manage_options', 'edit-tags.php?taxonomy=kv_tour_type&post_type=kv_tour' );
}, 20 );
add_filter( 'parent_file', function ( $parent ) {
	global $current_screen;
	if ( $current_screen && in_array( $current_screen->taxonomy, array( 'kv_destination', 'kv_tour_type' ), true ) ) {
		return 'kv-dashboard';
	}
	return $parent;
} );
add_action( 'admin_menu', function () {
	add_submenu_page( 'kv-dashboard', 'Website Settings', 'Settings', 'manage_options', 'kv-settings', 'kv_render_settings' );
}, 99 );

add_action( 'admin_enqueue_scripts', 'kv_admin_assets' );
function kv_admin_assets() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$is_cpt = $screen && in_array( $screen->post_type, array( 'kv_tour', 'kv_review', 'kv_gallery', 'kv_faq' ), true );
	$page   = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! $is_cpt && 0 !== strpos( $page, 'kv-' ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'kv-admin', KV_URL . 'assets/admin.css', array(), KV_VERSION );
	wp_enqueue_script( 'kv-admin', KV_URL . 'assets/admin.js', array( 'jquery' ), KV_VERSION, true );
}

/* ============ Small helpers ============ */
function kv_badge( $status ) {
	$l = kv_statuses();
	return '<span class="kv-badge kv-st-' . esc_attr( $status ) . '">' . esc_html( isset( $l[ $status ] ) ? $l[ $status ] : $status ) . '</span>';
}

function kv_print_notice() {
	$key = 'kv_notice_' . get_current_user_id();
	$n   = get_transient( $key );
	if ( $n ) {
		delete_transient( $key );
		echo '<div class="notice notice-' . ( 'error' === $n['type'] ? 'error' : 'success' ) . ' is-dismissible"><p>' . esc_html( $n['text'] ) . '</p></div>';
	}
}

function kv_pagination( $total, $per, $paged ) {
	$pages = max( 1, (int) ceil( $total / $per ) );
	if ( $pages < 2 ) {
		return;
	}
	echo '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post( paginate_links( array( 'base' => add_query_arg( 'paged', '%#%' ), 'format' => '', 'current' => $paged, 'total' => $pages ) ) ) . '</div></div>';
}

/* ============ Dashboard ============ */
function kv_render_dashboard() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $wpdb;
	$t      = $wpdb->prefix . 'kv_bookings';
	$counts = $wpdb->get_results( "SELECT status, COUNT(*) AS c FROM $t GROUP BY status", OBJECT_K );
	$c      = function ( $s ) use ( $counts ) {
		return isset( $counts[ $s ] ) ? (int) $counts[ $s ]->c : 0;
	};
	$total     = 0;
	foreach ( $counts as $row ) {
		$total += (int) $row->c;
	}
	$customers = (int) $wpdb->get_var( "SELECT COUNT(DISTINCT email) FROM $t" );
	$tours     = (int) wp_count_posts( 'kv_tour' )->publish;
	$received  = (float) $wpdb->get_var( "SELECT COALESCE(SUM(paid_amount),0) FROM $t WHERE status<>'cancelled'" );
	$cards     = array(
		'Total tour packages'    => $tours,
		'Total bookings'         => $total,
		'New inquiries'          => $c( 'new' ),
		'Confirmed bookings'     => $c( 'confirmed' ),
		'Pending payments'       => $c( 'awaiting_payment' ),
		'Completed tours'        => $c( 'completed' ),
		'Cancelled bookings'     => $c( 'cancelled' ),
		'Total customers'        => $customers,
	);
	echo '<div class="wrap kv-wrap"><h1>Kashmiri Ventures Dashboard</h1>';
	kv_print_notice();
	echo '<div class="kv-cards">';
	foreach ( $cards as $label => $num ) {
		echo '<div class="kv-card"><div class="kv-card-n">' . esc_html( number_format_i18n( $num ) ) . '</div><div class="kv-card-l">' . esc_html( $label ) . '</div></div>';
	}
	echo '<div class="kv-card kv-card-gold"><div class="kv-card-n">' . kv_money( $received ) . '</div><div class="kv-card-l">Verified payments received</div></div></div>';

	echo '<div class="kv-two"><div class="kv-panel"><h2>Bookings by status</h2>';
	$max = 1;
	foreach ( kv_statuses() as $k => $l ) {
		$max = max( $max, $c( $k ) );
	}
	foreach ( kv_statuses() as $k => $l ) {
		$n = $c( $k );
		echo '<div class="kv-bar"><span class="kv-bar-l">' . esc_html( $l ) . '</span><span class="kv-bar-track"><span class="kv-bar-fill kv-st-' . esc_attr( $k ) . '" style="width:' . esc_attr( round( $n / $max * 100 ) ) . '%"></span></span><span class="kv-bar-n">' . (int) $n . '</span></div>';
	}
	echo '</div><div class="kv-panel"><h2>Quick links</h2><p><a class="button button-primary" href="' . esc_url( admin_url( 'post-new.php?post_type=kv_tour' ) ) . '">Add a tour</a> <a class="button" href="' . esc_url( admin_url( 'admin.php?page=kv-bookings&status=new' ) ) . '">New inquiries</a> <a class="button" href="' . esc_url( admin_url( 'admin.php?page=kv-settings' ) ) . '">Website settings</a> <a class="button" href="' . esc_url( home_url( '/' ) ) . '" target="_blank" rel="noopener">View website</a></p>';
	echo '<p class="description">Seats are held by bookings that are Awaiting Payment, Payment Received, Booking Confirmed or Completed. New Inquiry, Contacted and Cancelled bookings do not hold seats.</p></div></div>';

	echo '<h2>Recent bookings</h2>';
	$rows = $wpdb->get_results( "SELECT * FROM $t ORDER BY id DESC LIMIT 10" );
	kv_bookings_table( $rows );
	echo '</div>';
}

function kv_bookings_table( $rows ) {
	if ( ! $rows ) {
		echo '<p>No bookings yet. They appear here when a customer submits the booking form.</p>';
		return;
	}
	echo '<table class="widefat striped kv-table"><thead><tr><th>Reference</th><th>Customer</th><th>Tour</th><th>Departure</th><th>Travellers</th><th>Total</th><th>Payment</th><th>Status</th><th>Received</th></tr></thead><tbody>';
	foreach ( $rows as $b ) {
		$url = admin_url( 'admin.php?page=kv-bookings&booking=' . (int) $b->id );
		echo '<tr><td><a href="' . esc_url( $url ) . '"><strong>' . esc_html( $b->reference ) . '</strong></a></td>';
		echo '<td>' . esc_html( $b->customer_name ) . '<br><small>' . esc_html( $b->phone ) . '</small></td>';
		echo '<td>' . esc_html( $b->tour_title ) . '</td>';
		echo '<td>' . esc_html( $b->departure_date ? kv_date( $b->departure_date ) : 'Not set' ) . '</td>';
		echo '<td>' . (int) $b->travellers . '</td>';
		echo '<td>' . kv_money( $b->total_amount ) . '</td>';
		echo '<td>' . esc_html( ucfirst( $b->payment_status ) ) . '</td>';
		echo '<td>' . kv_badge( $b->status ) . '</td>';
		echo '<td>' . esc_html( mysql2date( 'j M Y, g:i a', $b->created_at ) ) . '</td></tr>';
	}
	echo '</tbody></table>';
}

/* ============ Bookings ============ */
function kv_render_bookings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_GET['booking'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		kv_render_booking_detail( absint( $_GET['booking'] ) );
		return;
	}
	global $wpdb;
	$t     = $wpdb->prefix . 'kv_bookings';
	$where = array( '1=1' );
	$args  = array();
	$gq    = function ( $k ) {
		return isset( $_GET[ $k ] ) ? sanitize_text_field( wp_unslash( $_GET[ $k ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	};
	$q      = $gq( 'q' );
	$status = $gq( 's' ) ? $gq( 's' ) : $gq( 'status' );
	$dest   = absint( $gq( 'dest' ) );
	$from   = $gq( 'from' );
	$to     = $gq( 'to' );
	if ( '' !== $q ) {
		$like    = '%' . $wpdb->esc_like( $q ) . '%';
		$where[] = '(reference LIKE %s OR customer_name LIKE %s OR email LIKE %s OR phone LIKE %s)';
		array_push( $args, $like, $like, $like, $like );
	}
	if ( $status && isset( kv_statuses()[ $status ] ) ) {
		$where[] = 'status=%s';
		$args[]  = $status;
	}
	if ( $dest ) {
		$ids = get_posts( array( 'post_type' => 'kv_tour', 'post_status' => 'any', 'fields' => 'ids', 'posts_per_page' => -1, 'tax_query' => array( array( 'taxonomy' => 'kv_destination', 'field' => 'term_id', 'terms' => $dest ) ) ) );
		$where[] = $ids ? 'tour_id IN (' . implode( ',', array_map( 'absint', $ids ) ) . ')' : '1=0';
	}
	if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $from ) ) {
		$where[] = 'DATE(created_at)>=%s';
		$args[]  = $from;
	}
	if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $to ) ) {
		$where[] = 'DATE(created_at)<=%s';
		$args[]  = $to;
	}
	$w     = implode( ' AND ', $where );
	$per   = 20;
	$paged = max( 1, (int) $gq( 'paged' ) );
	$count = "SELECT COUNT(*) FROM $t WHERE $w";
	$total = (int) ( $args ? $wpdb->get_var( $wpdb->prepare( $count, $args ) ) : $wpdb->get_var( $count ) );
	$sql   = "SELECT * FROM $t WHERE $w ORDER BY id DESC LIMIT %d OFFSET %d";
	$rows  = $wpdb->get_results( $wpdb->prepare( $sql, array_merge( $args, array( $per, ( $paged - 1 ) * $per ) ) ) );

	echo '<div class="wrap kv-wrap"><h1>Bookings</h1>';
	kv_print_notice();
	echo '<form method="get" class="kv-filters"><input type="hidden" name="page" value="kv-bookings">';
	echo '<input type="search" name="q" value="' . esc_attr( $q ) . '" placeholder="Search reference, name, email, phone">';
	echo '<select name="status"><option value="">All statuses</option>';
	foreach ( kv_statuses() as $k => $l ) {
		echo '<option value="' . esc_attr( $k ) . '"' . selected( $status, $k, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select>';
	wp_dropdown_categories( array( 'taxonomy' => 'kv_destination', 'name' => 'dest', 'selected' => $dest, 'show_option_all' => 'All destinations', 'hide_empty' => 0, 'hierarchical' => 1 ) );
	echo '<label>From <input type="date" name="from" value="' . esc_attr( $from ) . '"></label><label>To <input type="date" name="to" value="' . esc_attr( $to ) . '"></label>';
	echo '<button class="button button-primary">Filter</button> <a class="button" href="' . esc_url( admin_url( 'admin.php?page=kv-bookings' ) ) . '">Reset</a></form>';
	echo '<p>' . (int) $total . ' booking' . ( 1 === $total ? '' : 's' ) . ' found.</p>';
	kv_bookings_table( $rows );
	kv_pagination( $total, $per, $paged );
	echo '</div>';
}

function kv_render_booking_detail( $id ) {
	global $wpdb;
	$b = kv_get_booking( $id );
	echo '<div class="wrap kv-wrap">';
	if ( ! $b ) {
		echo '<h1>Booking not found</h1><p><a href="' . esc_url( admin_url( 'admin.php?page=kv-bookings' ) ) . '">Back to bookings</a></p></div>';
		return;
	}
	kv_print_notice();
	echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=kv-bookings' ) ) . '">&larr; All bookings</a></p>';
	echo '<h1>Booking ' . esc_html( $b->reference ) . ' ' . kv_badge( $b->status ) . '</h1>';

	$seat_line = 'No departure date set';
	if ( $b->departure_date ) {
		$seat_line = kv_date( $b->departure_date );
		foreach ( kv_get_departures( $b->tour_id, false ) as $d ) {
			if ( $d['date'] === $b->departure_date ) {
				$seat_line .= ' (' . $d['booked'] . ' of ' . $d['seats'] . ' seats held)';
			}
		}
	}
	echo '<div class="kv-two"><div class="kv-panel"><h2>Customer and trip</h2><table class="kv-dl">';
	$rows = array(
		'Customer'         => $b->customer_name,
		'Email'            => $b->email,
		'Phone'            => $b->phone,
		'WhatsApp'         => $b->whatsapp,
		'Tour'             => $b->tour_title,
		'Departure'        => $seat_line,
		'Travellers'       => $b->travellers . ' (' . $b->adults . ' adults, ' . $b->children . ' children)',
		'Package / room'   => ( ! empty( $b->package_tier ) ) ? ( ( isset( kv_package_tiers()[ $b->package_tier ] ) ? kv_package_tiers()[ $b->package_tier ] : $b->package_tier ) . ' / ' . ( isset( kv_room_types()[ $b->room_type ] ) ? kv_room_types()[ $b->room_type ] : $b->room_type ) ) : '-',
		'Special requests' => $b->special_requests,
		'More information' => $b->additional_info,
		'Submitted'        => mysql2date( 'j M Y, g:i a', $b->created_at ),
	);
	foreach ( $rows as $k => $v ) {
		echo '<tr><th>' . esc_html( $k ) . '</th><td>' . nl2br( esc_html( $v ) ) . '</td></tr>';
	}
	$wa = preg_replace( '/\D+/', '', $b->whatsapp );
	echo '</table><p>';
	if ( $wa ) {
		echo '<a class="button" target="_blank" rel="noopener" href="' . esc_url( 'https://wa.me/' . $wa ) . '">Open WhatsApp chat</a> ';
	}
	echo '<a class="button" href="' . esc_url( 'mailto:' . $b->email ) . '">Email customer</a> <a class="button" href="' . esc_url( admin_url( 'admin.php?page=kv-customers&email=' . rawurlencode( $b->email ) ) ) . '">Customer history</a></p></div>';

	echo '<div class="kv-panel"><h2>Update booking</h2><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'kv_update_booking_' . $id );
	echo '<input type="hidden" name="action" value="kv_update_booking"><input type="hidden" name="booking_id" value="' . (int) $id . '">';
	echo '<p><label><strong>Booking status</strong><br><select name="status">';
	foreach ( kv_statuses() as $k => $l ) {
		echo '<option value="' . esc_attr( $k ) . '"' . selected( $b->status, $k, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select></label></p>';
	echo '<p><label><strong>Total package amount (' . esc_html( kv_opt( 'currency' ) ) . ')</strong><br><input type="number" step="1" min="0" name="total_amount" value="' . esc_attr( $b->total_amount ) . '"></label></p>';
	echo '<p><label><strong>Internal admin notes</strong> (never shown to the customer)<br><textarea name="admin_notes" rows="5" class="large-text">' . esc_textarea( $b->admin_notes ) . '</textarea></label></p>';
	echo '<p><button class="button button-primary">Save changes</button></p></form></div></div>';

	/* Payments */
	$pay     = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}kv_payments WHERE booking_id=%d ORDER BY id DESC", $id ) );
	$balance = max( 0, (float) $b->total_amount - (float) $b->paid_amount );
	echo '<div class="kv-panel"><h2>Payment records</h2><p>Total ' . kv_money( $b->total_amount ) . ' &middot; Verified paid ' . kv_money( $b->paid_amount ) . ' &middot; Balance ' . kv_money( $balance ) . ' &middot; Payment status: <strong>' . esc_html( ucfirst( $b->payment_status ) ) . '</strong></p>';
	echo '<p class="description">A payment counts as received only after you mark it Verified (for example after you checked your bank or wallet).</p>';
	if ( $pay ) {
		echo '<table class="widefat striped"><thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th><th>Status</th><th>Notes</th><th></th></tr></thead><tbody>';
		foreach ( $pay as $p ) {
			$new = 'verified' === $p->status ? 'pending' : 'verified';
			echo '<tr><td>' . esc_html( kv_date( $p->paid_on ) ) . '</td><td>' . kv_money( $p->amount ) . '</td><td>' . esc_html( $p->method ) . '</td><td>' . esc_html( $p->txn_ref ) . '</td><td>' . esc_html( ucfirst( $p->status ) ) . '</td><td>' . esc_html( $p->notes ) . '</td><td>';
			echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
			wp_nonce_field( 'kv_payment_status_' . $p->id );
			echo '<input type="hidden" name="action" value="kv_payment_status"><input type="hidden" name="payment_id" value="' . (int) $p->id . '"><input type="hidden" name="new_status" value="' . esc_attr( $new ) . '"><button class="button button-small">' . ( 'verified' === $new ? 'Mark verified' : 'Mark pending' ) . '</button></form></td></tr>';
		}
		echo '</tbody></table>';
	}
	echo '<h3>Add payment record</h3><form method="post" class="kv-payform" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'kv_add_payment_' . $id );
	echo '<input type="hidden" name="action" value="kv_add_payment"><input type="hidden" name="booking_id" value="' . (int) $id . '">';
	echo '<label>Amount<br><input type="number" step="1" min="1" name="amount" required></label>';
	echo '<label>Payment date<br><input type="date" name="paid_on" value="' . esc_attr( current_time( 'Y-m-d' ) ) . '"></label>';
	echo '<label>Method<br><select name="method"><option>Bank transfer</option><option>Cash</option><option>JazzCash</option><option>Easypaisa</option><option>Card (manual)</option><option>Other</option></select></label>';
	echo '<label>Transaction / reference no.<br><input type="text" name="txn_ref"></label>';
	echo '<label>Status<br><select name="pay_status"><option value="pending">Pending (not checked yet)</option><option value="verified">Verified (money received)</option></select></label>';
	echo '<label class="kv-wide">Notes<br><input type="text" name="pay_notes" class="large-text"></label>';
	echo '<p><button class="button">Save payment record</button></p></form></div>';

	/* History */
	$log = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}kv_booking_log WHERE booking_id=%d ORDER BY id DESC", $id ) );
	echo '<div class="kv-panel"><h2>Booking history</h2><table class="widefat striped"><tbody>';
	foreach ( $log as $l ) {
		echo '<tr><td style="width:190px">' . esc_html( mysql2date( 'j M Y, g:i a', $l->created_at ) ) . '</td><td style="width:160px">' . esc_html( $l->actor ) . '</td><td>' . esc_html( $l->entry ) . '</td></tr>';
	}
	echo '</tbody></table></div></div>';
}

/* ============ Customers ============ */
function kv_render_customers() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $wpdb;
	$t = $wpdb->prefix . 'kv_bookings';
	echo '<div class="wrap kv-wrap"><h1>Customers</h1>';
	$email = isset( $_GET['email'] ) ? sanitize_email( wp_unslash( $_GET['email'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	if ( $email ) {
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $t WHERE email=%s ORDER BY id DESC", $email ) );
		echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=kv-customers' ) ) . '">&larr; All customers</a></p>';
		if ( $rows ) {
			echo '<div class="kv-panel"><h2>' . esc_html( $rows[0]->customer_name ) . '</h2><p>Email: ' . esc_html( $email ) . ' &middot; Phone: ' . esc_html( $rows[0]->phone ) . ' &middot; WhatsApp: ' . esc_html( $rows[0]->whatsapp ) . '</p></div><h2>Booking history</h2>';
		}
		kv_bookings_table( $rows );
		echo '</div>';
		return;
	}
	$q    = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$w    = '1=1';
	$args = array();
	if ( '' !== $q ) {
		$like = '%' . $wpdb->esc_like( $q ) . '%';
		$w    = '(customer_name LIKE %s OR email LIKE %s OR phone LIKE %s)';
		$args = array( $like, $like, $like );
	}
	$sql  = "SELECT email, MAX(customer_name) AS name, MAX(phone) AS phone, COUNT(*) AS bookings, MAX(created_at) AS last_booking, SUM(CASE WHEN status<>'cancelled' THEN total_amount ELSE 0 END) AS value FROM $t WHERE $w GROUP BY email ORDER BY last_booking DESC LIMIT 200";
	$rows = $args ? $wpdb->get_results( $wpdb->prepare( $sql, $args ) ) : $wpdb->get_results( $sql );
	echo '<form method="get" class="kv-filters"><input type="hidden" name="page" value="kv-customers"><input type="search" name="q" value="' . esc_attr( $q ) . '" placeholder="Search name, email, phone"><button class="button button-primary">Search</button></form>';
	if ( ! $rows ) {
		echo '<p>No customers yet. Customers are created automatically from bookings.</p></div>';
		return;
	}
	echo '<table class="widefat striped kv-table"><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Bookings</th><th>Booking value (not cancelled)</th><th>Last booking</th></tr></thead><tbody>';
	foreach ( $rows as $r ) {
		$url = admin_url( 'admin.php?page=kv-customers&email=' . rawurlencode( $r->email ) );
		echo '<tr><td><a href="' . esc_url( $url ) . '"><strong>' . esc_html( $r->name ) . '</strong></a></td><td>' . esc_html( $r->email ) . '</td><td>' . esc_html( $r->phone ) . '</td><td>' . (int) $r->bookings . '</td><td>' . kv_money( $r->value ) . '</td><td>' . esc_html( mysql2date( 'j M Y', $r->last_booking ) ) . '</td></tr>';
	}
	echo '</tbody></table></div>';
}

/* ============ Settings ============ */
function kv_settings_fields() {
	return array(
		'Logo and homepage banner' => array(
			'logo_id'    => array( 'Logo', 'media', 'Upload the final logo (transparent PNG or SVG). Leave empty to keep the temporary logo.' ),
			'hero_id'    => array( 'Hero banner image', 'media', 'Wide landscape photo, at least 1920 pixels wide.' ),
			'hero_link'  => array( 'Or paste a hero photo link', 'url', 'Paste a direct image link (right-click a photo, Copy image address). On Save, it is downloaded into the Media Library and used as the hero banner.' ),
			'hero_title' => array( 'Hero headline', 'text', '' ),
			'hero_text'  => array( 'Hero description', 'textarea', '' ),
			'hero_btn1'  => array( 'Hero button 1 text', 'text', 'Links to All Tours.' ),
			'hero_btn2'  => array( 'Hero button 2 text', 'text', 'Links to the booking page.' ),
		),
		'Homepage sections' => array(
			'search_title'     => array( 'Search box heading', 'text', '' ),
			'popular_title'    => array( 'Popular tours heading', 'text', 'Tours marked Featured show here.' ),
			'popular_text'     => array( 'Popular tours description', 'text', '' ),
			'dest_title'       => array( 'Destination sections heading', 'text', 'The destination sections themselves are edited in the panel further down this page.' ),
			'dest_text'        => array( 'Destination sections description', 'text', '' ),
			'strip_items'      => array( 'Strip under the banner', 'textarea', 'Three short points shown under the homepage banner. One per line, written as: Title|Short text' ),
			'why_title'        => array( 'Why choose us heading', 'text', '' ),
			'stat_clients'     => array( 'Happy clients number', 'text', 'Example: 50000+. Shown in the Why choose us section.' ),
			'why_text'         => array( 'Why choose us description', 'text', '' ),
			'why_items'        => array( 'Why choose us points', 'textarea', 'One point per line, written as: Title|Short description' ),
			'departures_title' => array( 'Upcoming departures heading', 'text', 'Filled automatically from tour departure dates.' ),
			'reviews_title'    => array( 'Reviews heading', 'text', 'Reviews are managed under Kashmiri Ventures, then Reviews.' ),
			'how_title'        => array( 'How booking works heading', 'text', '' ),
			'cta_title'        => array( 'Final call-to-action heading', 'text', '' ),
			'cta_text'         => array( 'Final call-to-action text', 'text', '' ),
		),
		'Common trip information (homepage and every tour page)' => array(
			'info_title'     => array( 'Section heading', 'text', 'Heading of the "How we travel" section on the homepage.' ),
			'transport_text' => array( 'Transportation', 'textarea', '' ),
			'food_breakfast' => array( 'Breakfast menu', 'text', '' ),
			'food_dinner'    => array( 'Dinner menu', 'text', '' ),
			'food_note'      => array( 'Food note', 'text', '' ),
			'inc_common'     => array( 'Services included (all tours)', 'textarea', 'One per line. A tour with its own Included list shows that list instead.' ),
			'exc_common'     => array( 'Services not included (all tours)', 'textarea', 'One per line. A tour with its own Not included list shows that list instead.' ),
			'cities'         => array( 'Pick and drop cities', 'textarea', 'One per line, written as: City|Pick-up point' ),
			'cities_notes'   => array( 'Pick and drop notes', 'textarea', 'One note per line.' ),
		),
		'About Us page' => array(
			'about_title'  => array( 'About heading', 'text', '' ),
			'about_text'   => array( 'About text', 'textarea', 'Separate paragraphs with a blank line.' ),
			'about_img_id' => array( 'About image', 'media', '' ),
		),
		'Contact details' => array(
			'phone'        => array( 'Phone number', 'text', 'Shown in the header, footer and contact page. Use the international format, for example +92 329 8121084.' ),
			'phone2'       => array( 'Second phone number', 'text', 'Shown in the footer and on the contact page. Leave empty to hide.' ),
			'whatsapp'     => array( 'WhatsApp number', 'text', 'Use the international format without + or spaces. Example: 923001234567' ),
			'email'        => array( 'Public email address', 'email', '' ),
			'address'      => array( 'Business address', 'text', '' ),
			'notify_email' => array( 'Booking notification email', 'email', 'New bookings are emailed here. Leave empty to use the WordPress admin email.' ),
			'currency'     => array( 'Currency label', 'text', 'Example: PKR' ),
		),
		'Footer and social links' => array(
			'footer_text' => array( 'Footer description', 'text', '' ),
			'facebook'    => array( 'Facebook URL', 'url', '' ),
			'instagram'   => array( 'Instagram URL', 'url', '' ),
			'youtube'     => array( 'YouTube URL', 'url', '' ),
			'tiktok'      => array( 'TikTok URL', 'url', '' ),
		),
	);
}

add_action( 'admin_post_kv_save_settings', 'kv_save_settings' );
function kv_save_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You are not allowed to do this.' );
	}
	check_admin_referer( 'kv_save_settings' );
	$in  = isset( $_POST['kv'] ) ? (array) wp_unslash( $_POST['kv'] ) : array();
	$out = array();
	foreach ( kv_settings_fields() as $fields ) {
		foreach ( $fields as $key => $f ) {
			$v = isset( $in[ $key ] ) ? $in[ $key ] : '';
			switch ( $f[1] ) {
				case 'media':
					$out[ $key ] = absint( $v );
					break;
				case 'textarea':
					$out[ $key ] = sanitize_textarea_field( $v );
					break;
				case 'url':
					$out[ $key ] = esc_url_raw( $v );
					break;
				case 'email':
					$out[ $key ] = sanitize_email( $v );
					break;
				default:
					$out[ $key ] = sanitize_text_field( $v );
			}
		}
	}
	$rows    = array();
	$dest_in = isset( $_POST['kv_dest'] ) ? (array) wp_unslash( $_POST['kv_dest'] ) : array();
	foreach ( $dest_in as $r ) {
		if ( ! is_array( $r ) ) {
			continue;
		}
		$name = isset( $r['name'] ) ? sanitize_text_field( $r['name'] ) : '';
		if ( '' === $name ) {
			continue;
		}
		$tid    = isset( $r['tour_id'] ) ? absint( $r['tour_id'] ) : 0;
		$prices = kv_clean_packages( ( isset( $r['prices'] ) && is_array( $r['prices'] ) ) ? $r['prices'] : array() );
		$rows[] = array(
			'show'     => ! empty( $r['show'] ) ? 1 : 0,
			'name'     => $name,
			'heading'  => isset( $r['heading'] ) ? sanitize_text_field( $r['heading'] ) : '',
			'text'     => isset( $r['text'] ) ? sanitize_textarea_field( $r['text'] ) : '',
			'image_id' => isset( $r['image_id'] ) ? absint( $r['image_id'] ) : 0,
			'tour_id'  => $tid,
			'prices'   => $prices,
		);
		if ( $tid && 'kv_tour' === get_post_type( $tid ) ) {
			if ( $prices ) {
				update_post_meta( $tid, '_kv_packages', $prices );
			} else {
				delete_post_meta( $tid, '_kv_packages' );
			}
		}
	}
	$out['dest_sections'] = $rows;

	$old  = (array) get_option( 'kv_settings', array() );
	$prev = isset( $old['hero_link'] ) ? $old['hero_link'] : '';
	$msg  = 'Settings saved.';
	$type = 'success';
	if ( ! empty( $out['hero_link'] ) && $out['hero_link'] !== $prev ) {
		$id = kv_sideload( $out['hero_link'], 0 );
		if ( is_wp_error( $id ) ) {
			$msg  = 'Settings saved, but the hero photo link could not be imported: ' . $id->get_error_message();
			$type = 'error';
		} else {
			$out['hero_id'] = (int) $id;
		}
	}
	update_option( 'kv_settings', $out );
	kv_flash( $type, $msg );
	wp_safe_redirect( admin_url( 'admin.php?page=kv-settings' ) );
	exit;
}

function kv_render_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="wrap kv-wrap"><h1>Website Settings</h1>';
	kv_print_notice();
	echo '<p>Everything on the website that is not a tour, review, gallery photo or FAQ is edited here. Policy pages are normal WordPress pages (Pages menu).</p>';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="kv_save_settings">';
	wp_nonce_field( 'kv_save_settings' );
	foreach ( kv_settings_fields() as $section => $fields ) {
		echo '<div class="kv-panel"><h2>' . esc_html( $section ) . '</h2><table class="form-table" role="presentation">';
		foreach ( $fields as $key => $f ) {
			$val = kv_opt( $key );
			$nm  = 'kv[' . $key . ']';
			echo '<tr><th scope="row"><label>' . esc_html( $f[0] ) . '</label></th><td>';
			if ( 'media' === $f[1] ) {
				echo '<div class="kv-media"><input type="hidden" name="' . esc_attr( $nm ) . '" value="' . esc_attr( (int) $val ) . '"><div class="kv-media-prev">' . ( $val ? wp_get_attachment_image( (int) $val, 'medium' ) : '' ) . '</div><button type="button" class="button kv-media-pick">Choose image</button> <button type="button" class="button-link kv-media-clear">Remove</button></div>';
			} elseif ( 'textarea' === $f[1] ) {
				echo '<textarea name="' . esc_attr( $nm ) . '" rows="6" class="large-text">' . esc_textarea( $val ) . '</textarea>';
			} else {
				echo '<input type="' . esc_attr( 'url' === $f[1] ? 'url' : ( 'email' === $f[1] ? 'email' : 'text' ) ) . '" name="' . esc_attr( $nm ) . '" value="' . esc_attr( $val ) . '" class="regular-text">';
			}
			if ( $f[2] ) {
				echo '<p class="description">' . esc_html( $f[2] ) . '</p>';
			}
			echo '</td></tr>';
		}
		echo '</table></div>';
	}
	echo '<div class="kv-panel"><h2>Homepage destination sections</h2><p class="description">Each section shows a destination on the homepage with a photo, text and package prices. Change the heading, text, photo and prices here, or tick/untick Show. When a tour is linked, the prices you enter here are also the prices of that tour (used for booking totals). Leave all prices empty to show "Price on request".</p><div class="kv-repeater"><div class="kv-rows">';
	$sections = kv_opt( 'dest_sections' );
	$sections = is_array( $sections ) ? $sections : array();
	foreach ( $sections as $i => $r ) {
		kv_dest_row( $i, $r );
	}
	echo '</div><script type="text/template" class="kv-row-tpl">';
	kv_dest_row( '__i__', array( 'show' => 1 ) );
	echo '</script><p><button type="button" class="button kv-add-row">Add destination section</button></p></div></div>';
	echo '<p><button class="button button-primary button-hero">Save settings</button></p></form>';

	echo '<div class="kv-panel"><h2>Demo content</h2><p>Adds sample destinations, tours, departure dates, reviews, FAQs and gallery items so you can see the whole website working. Remove it before the real launch. Removing deletes only items created by this button.</p>';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline">';
	wp_nonce_field( 'kv_demo' );
	echo '<input type="hidden" name="action" value="kv_load_demo"><button class="button">Load demo content</button></form> ';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline" onsubmit="return confirm(\'Remove all demo tours, reviews, FAQs and gallery items?\');">';
	wp_nonce_field( 'kv_demo' );
	echo '<input type="hidden" name="action" value="kv_remove_demo"><button class="button">Remove demo content</button></form></div>';
	echo '<div class="kv-panel"><h2>Kashmiri Ventures tours and policies</h2><p>Imports the real tours and itineraries, FAQs and policy pages (terms, cancellation, refund, booking) from the WhatsApp catalogue, and removes the demo items. Tours with no price show "Price on request" until you enter a price in the tour. Policy pages you already edited are not overwritten.</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'kv_demo' );
	echo '<input type="hidden" name="action" value="kv_import_real"><button class="button button-primary">Import Kashmiri Ventures tours and policies</button></form></div></div>';
}


/* One editable destination section (Settings page). */
function kv_dest_row( $i, $r ) {
	$r   = wp_parse_args( (array) $r, array( 'show' => 1, 'name' => '', 'heading' => '', 'text' => '', 'image_id' => 0, 'tour_id' => 0, 'prices' => array() ) );
	$tid = (int) $r['tour_id'];
	$pk  = $tid ? kv_tour_packages( $tid ) : array();
	if ( ! $pk ) {
		$pk = kv_clean_packages( (array) $r['prices'] );
	}
	$tours = get_posts( array( 'post_type' => 'kv_tour', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
	$n     = 'kv_dest[' . $i . ']';
	echo '<div class="kv-row kv-dest-row">';
	echo '<label class="kv-check"><input type="checkbox" name="' . esc_attr( $n ) . '[show]" value="1"' . checked( ! empty( $r['show'] ), true, false ) . '> <strong>Show on homepage</strong></label>';
	echo '<label class="kv-f"><span>Destination name</span><input type="text" name="' . esc_attr( $n ) . '[name]" value="' . esc_attr( $r['name'] ) . '"></label>';
	echo '<label class="kv-f"><span>Heading</span><input type="text" name="' . esc_attr( $n ) . '[heading]" value="' . esc_attr( $r['heading'] ) . '"></label>';
	echo '<label class="kv-f full"><span>Text</span><textarea name="' . esc_attr( $n ) . '[text]" rows="3">' . esc_textarea( $r['text'] ) . '</textarea></label>';
	echo '<div class="kv-f"><span>Photo</span><div class="kv-media"><input type="hidden" name="' . esc_attr( $n ) . '[image_id]" value="' . (int) $r['image_id'] . '"><div class="kv-media-prev">' . ( $r['image_id'] ? wp_get_attachment_image( (int) $r['image_id'], 'medium' ) : '' ) . '</div><button type="button" class="button kv-media-pick">Choose photo</button> <button type="button" class="button-link kv-media-clear">Remove</button></div></div>';
	echo '<label class="kv-f"><span>Linked tour (for View tour and Book Now buttons)</span><select name="' . esc_attr( $n ) . '[tour_id]"><option value="0">No tour</option>';
	foreach ( $tours as $t ) {
		echo '<option value="' . (int) $t->ID . '"' . selected( $tid, $t->ID, false ) . '>' . esc_html( $t->post_title ) . '</option>';
	}
	echo '</select></label>';
	echo '<div class="kv-f full"><span>Package prices (' . esc_html( kv_opt( 'currency' ) ) . ')</span><table class="widefat kv-price-grid"><thead><tr><th></th>';
	foreach ( kv_room_types() as $rl ) {
		echo '<th>' . esc_html( $rl ) . '</th>';
	}
	echo '</tr></thead><tbody>';
	foreach ( kv_package_tiers() as $t => $tl ) {
		echo '<tr><th>' . esc_html( $tl ) . '</th>';
		foreach ( kv_room_types() as $rk => $rl ) {
			$v = ( isset( $pk[ $t ][ $rk ] ) && $pk[ $t ][ $rk ] > 0 ) ? $pk[ $t ][ $rk ] : '';
			echo '<td><input type="number" min="0" step="1" name="' . esc_attr( $n ) . '[prices][' . esc_attr( $t ) . '][' . esc_attr( $rk ) . ']" value="' . esc_attr( $v ) . '"></td>';
		}
		echo '</tr>';
	}
	echo '</tbody></table></div>';
	echo '<button type="button" class="button-link kv-remove-row">Remove this section</button></div>';
}
