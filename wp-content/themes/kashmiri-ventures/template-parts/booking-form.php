<?php
$tours = get_posts( array( 'post_type' => 'kv_tour', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
$data  = array();
foreach ( $tours as $t ) {
	$deps = array();
	foreach ( kv_get_departures( $t->ID ) as $d ) {
		$deps[] = array( 'date' => $d['date'], 'label' => kv_date( $d['date'] ), 'remaining' => $d['remaining'] );
	}
	$p = (float) get_post_meta( $t->ID, '_kv_price', true );
	$c = get_post_meta( $t->ID, '_kv_child_price', true );
	$data[ $t->ID ] = array( 'title' => $t->post_title, 'price' => $p, 'child' => ( '' === $c || false === $c ) ? $p : (float) $c, 'deps' => $deps, 'packages' => kv_tour_packages( $t->ID ) );
}
$old = array();
if ( ! empty( $_GET['kv_form'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
	$o = get_transient( 'kv_form_' . sanitize_key( wp_unslash( $_GET['kv_form'] ) ) ); // phpcs:ignore
	if ( is_array( $o ) ) {
		$old = $o;
	}
}
$err      = isset( $_GET['kv_err'] ) ? sanitize_key( wp_unslash( $_GET['kv_err'] ) ) : ''; // phpcs:ignore
$val      = function ( $k, $d = '' ) use ( $old ) {
	return isset( $old[ $k ] ) ? $old[ $k ] : $d;
};
$sel_tour = $val( 'tour_id' ) ? (int) $val( 'tour_id' ) : ( isset( $_GET['tour'] ) ? absint( $_GET['tour'] ) : 0 ); // phpcs:ignore
$sel_date = $val( 'departure_date' ) ? $val( 'departure_date' ) : ( isset( $_GET['date'] ) ? sanitize_text_field( wp_unslash( $_GET['date'] ) ) : '' ); // phpcs:ignore
?>
<div class="booking-layout">
	<form id="booking-form" class="form-card" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
		data-pkg="<?php echo esc_attr( $val( 'package_tier' ) ); ?>" data-room="<?php echo esc_attr( $val( 'room_type' ) ); ?>" data-tour="<?php echo (int) $sel_tour; ?>" data-date="<?php echo esc_attr( $sel_date ); ?>" data-currency="<?php echo esc_attr( kv_opt( 'currency' ) ); ?>"
		data-wa="<?php echo esc_url( kv_whatsapp_url() ); ?>">
		<?php if ( $err ) : ?><div class="alert alert-error" role="alert"><?php echo esc_html( kv_booking_error_message( $err ) ); ?></div><?php endif; ?>
		<?php if ( ! $tours ) : ?><div class="alert alert-error">No tours are published yet, so booking is not available. Please contact us on WhatsApp.</div><?php endif; ?>
		<input type="hidden" name="action" value="kv_submit_booking">
		<?php wp_nonce_field( 'kv_booking', '_kvnonce' ); ?>
		<div class="hp" aria-hidden="true"><label>Website<input type="text" name="kv_website" tabindex="-1" autocomplete="off"></label></div>

		<fieldset>
			<legend>Your trip</legend>
			<div class="form-grid">
				<div class="field full"><label for="kv-tour">Tour package</label>
					<select id="kv-tour" name="tour_id" required>
						<option value="">Choose a tour</option>
						<?php foreach ( $tours as $t ) : ?><option value="<?php echo (int) $t->ID; ?>" <?php selected( $sel_tour, $t->ID ); ?>><?php echo esc_html( $t->post_title ); ?></option><?php endforeach; ?>
					</select></div>
				<div class="field full" id="kv-dep-wrap" hidden><label for="kv-dep">Departure date</label><select id="kv-dep" name="departure_date"></select></div>
				<div class="field full" id="kv-pref-wrap" hidden><label for="kv-pref">Preferred travel date (optional)</label><input id="kv-pref" type="date" name="preferred_date" min="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" value="<?php echo esc_attr( $val( 'preferred_date' ) ); ?>"></div>
				<div class="field full" id="kv-pkg-wrap" hidden><label for="kv-pkg">Package</label><select id="kv-pkg" name="package_tier"></select></div>
				<div class="field full" id="kv-room-wrap" hidden><label for="kv-room">Room type</label><select id="kv-room" name="room_type"></select></div>
				<div class="field"><label for="kv-adults">Adults</label><input id="kv-adults" type="number" name="adults" min="1" max="50" value="<?php echo (int) $val( 'adults', 2 ); ?>" required></div>
				<div class="field"><label for="kv-children">Children</label><input id="kv-children" type="number" name="children" min="0" max="50" value="<?php echo (int) $val( 'children', 0 ); ?>"></div>
			</div>
		</fieldset>

		<fieldset>
			<legend>Your details</legend>
			<div class="form-grid">
				<div class="field full"><label for="kv-name">Full name</label><input id="kv-name" type="text" name="name" autocomplete="name" required value="<?php echo esc_attr( $val( 'name' ) ); ?>"></div>
				<div class="field"><label for="kv-email">Email address</label><input id="kv-email" type="email" name="email" autocomplete="email" required value="<?php echo esc_attr( $val( 'email' ) ); ?>"></div>
				<div class="field"><label for="kv-phone">Phone number</label><input id="kv-phone" type="tel" name="phone" autocomplete="tel" required value="<?php echo esc_attr( $val( 'phone' ) ); ?>"></div>
				<div class="field full"><label for="kv-wa">WhatsApp number (if different)</label><input id="kv-wa" type="tel" name="whatsapp" value="<?php echo esc_attr( $val( 'whatsapp' ) ); ?>"></div>
				<div class="field full"><label for="kv-special">Special requests</label><textarea id="kv-special" name="special" maxlength="2000"><?php echo esc_textarea( $val( 'special' ) ); ?></textarea></div>
				<div class="field full"><label for="kv-info">Additional information</label><textarea id="kv-info" name="info" maxlength="2000"><?php echo esc_textarea( $val( 'info' ) ); ?></textarea></div>
			</div>
		</fieldset>
		<button class="btn btn-gold" type="submit"<?php echo $tours ? '' : ' disabled'; ?>>Send booking request</button>
		<p style="margin:14px 0 0;color:var(--muted);font-size:.95rem">This sends a booking request. It is not a confirmed booking until our team contacts you and the advance payment is agreed. See our <a href="<?php echo esc_url( kv_page_url( 'booking-policy' ) ); ?>">booking policies</a>.</p>
	</form>

	<aside class="estimate" aria-live="polite">
		<h3>Your estimate</h3>
		<div class="big" id="kv-est">Choose a tour</div>
		<p id="kv-est-note">The estimate is price per person times travellers. Our team confirms the final amount.</p>
		<p id="kv-seat-note"></p>
		<a class="btn btn-wa btn-block" id="kv-wa-quick" target="_blank" rel="noopener" href="<?php echo esc_url( kv_whatsapp_url( 'Hello, I would like to book a tour.' ) ); ?>" style="margin-top:14px">Book on WhatsApp</a>
	</aside>
</div>
<script type="application/json" id="kv-tours-data"><?php echo wp_json_encode( $data ); // phpcs:ignore ?></script>
