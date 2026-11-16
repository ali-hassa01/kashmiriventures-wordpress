<?php
get_header();
$ref = isset( $_GET['ref'] ) ? sanitize_text_field( wp_unslash( $_GET['ref'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$b   = $ref ? kv_get_booking_by_ref( $ref ) : null;
kv_page_head( 'Booking request received' );
?>
<section class="section"><div class="wrap prose">
<?php if ( $b ) : ?>
	<div class="alert alert-ok">Thank you. Your booking request has been saved and our team has been notified.</div>
	<p>Your booking reference:</p>
	<div class="ref-box"><?php echo esc_html( $b->reference ); ?></div>
	<p><strong>Tour:</strong> <?php echo esc_html( $b->tour_title ); ?><br>
	<strong>Departure:</strong> <?php echo esc_html( $b->departure_date ? kv_date( $b->departure_date ) : 'To be agreed' ); ?><br>
	<strong>Travellers:</strong> <?php echo (int) $b->travellers; ?><br>
	<strong>Estimated package amount:</strong> <?php echo (float) $b->total_amount > 0 ? kv_money( $b->total_amount ) : 'To be confirmed by our team'; // phpcs:ignore ?></p>
	<p>This is an acknowledgement, not a confirmation. We will contact you on <?php echo esc_html( $b->phone ); ?> or by email to confirm the details and the advance payment. A copy of this message has been sent to your email address.</p>
	<p><a class="btn btn-wa" target="_blank" rel="noopener" href="<?php echo esc_url( kv_whatsapp_url( 'Hello, my booking reference is ' . $b->reference . '.' ) ); ?>">Message us on WhatsApp</a> <a class="btn btn-line" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to home</a></p>
<?php else : ?>
	<p>We could not find a booking with that reference. If you just sent a booking, please contact us on WhatsApp and we will check it for you.</p>
	<p><a class="btn" href="<?php echo esc_url( kv_book_url() ); ?>">Go to booking form</a></p>
<?php endif; ?>
</div></section>
<?php get_footer();
