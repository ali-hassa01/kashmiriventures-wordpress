<?php
get_header();
kv_page_head( 'Contact Us', 'Ask a question, request a custom trip or check a date. We usually reply the same day.' );
$sent = isset( $_GET['sent'] ) ? sanitize_key( wp_unslash( $_GET['sent'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
?>
<section class="section"><div class="wrap contact-grid">
	<div>
		<ul class="contact-list">
			<li><strong>Phone</strong><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', kv_opt( 'phone' ) ) ); ?>"><?php echo esc_html( kv_opt( 'phone' ) ); ?></a><?php if ( kv_opt( 'phone2' ) ) : ?><br><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', kv_opt( 'phone2' ) ) ); ?>"><?php echo esc_html( kv_opt( 'phone2' ) ); ?></a><?php endif; ?></li>
			<li><strong>Email</strong><a href="mailto:<?php echo esc_attr( kv_opt( 'email' ) ); ?>"><?php echo esc_html( kv_opt( 'email' ) ); ?></a></li>
			<li><strong>Office</strong><?php echo esc_html( kv_opt( 'address' ) ); ?></li>
		</ul>
		<a class="btn btn-wa" target="_blank" rel="noopener" href="<?php echo esc_url( kv_whatsapp_url( 'Hello, I would like to ask about a tour.' ) ); ?>">Chat on WhatsApp</a>
		<a class="btn btn-line" href="<?php echo esc_url( kv_book_url() ); ?>">Book a tour</a>
	</div>
	<form class="form-card" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php if ( '1' === $sent ) : ?><div class="alert alert-ok" role="status">Thank you. Your message was sent.</div><?php elseif ( '0' === $sent ) : ?><div class="alert alert-error" role="alert">Your message could not be sent. Please check the fields or contact us on WhatsApp.</div><?php endif; ?>
		<input type="hidden" name="action" value="kv_contact">
		<?php wp_nonce_field( 'kv_contact', '_kvnonce' ); ?>
		<div class="hp" aria-hidden="true"><label>Website<input type="text" name="kv_website" tabindex="-1" autocomplete="off"></label></div>
		<div class="form-grid">
			<div class="field"><label for="c-name">Your name</label><input id="c-name" name="name" type="text" required></div>
			<div class="field"><label for="c-email">Email</label><input id="c-email" name="email" type="email" required></div>
			<div class="field full"><label for="c-phone">Phone (optional)</label><input id="c-phone" name="phone" type="tel"></div>
			<div class="field full"><label for="c-msg">Message</label><textarea id="c-msg" name="message" required minlength="5"></textarea></div>
		</div>
		<p style="margin-top:18px"><button class="btn" type="submit">Send message</button></p>
	</form>
</div></section>
<?php get_footer();
