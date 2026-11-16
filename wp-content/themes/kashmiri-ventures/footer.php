</main>
<footer class="site-footer">
	<div class="wrap">
		<div class="foot-grid">
			<div>
				<span class="logo-plate"><?php echo kv_logo_html(); // phpcs:ignore ?></span>
				<p style="margin-top:16px"><?php echo esc_html( kv_opt( 'footer_text' ) ); ?></p>
				<div class="social"><?php foreach ( kv_social_links() as $label => $url ) : ?><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $label ); ?>"><?php echo kv_social_icon( $label ); // phpcs:ignore ?><span><?php echo esc_html( $label ); ?></span></a><?php endforeach; ?></div>
			</div>
			<div>
				<h4>Contact</h4>
				<ul>
					<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', kv_opt( 'phone' ) ) ); ?>"><?php echo esc_html( kv_opt( 'phone' ) ); ?></a></li>
					<?php if ( kv_opt( 'phone2' ) ) : ?><li><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', kv_opt( 'phone2' ) ) ); ?>"><?php echo esc_html( kv_opt( 'phone2' ) ); ?></a></li><?php endif; ?>
					<li><a href="<?php echo esc_url( kv_whatsapp_url() ); ?>" target="_blank" rel="noopener">WhatsApp us</a></li>
					<li><a href="mailto:<?php echo esc_attr( kv_opt( 'email' ) ); ?>"><?php echo esc_html( kv_opt( 'email' ) ); ?></a></li>
					<li><?php echo esc_html( kv_opt( 'address' ) ); ?></li>
				</ul>
			</div>
			<div>
				<h4>Quick links</h4>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false ) );
				} else {
					echo '<ul>';
					foreach ( kv_nav_links() as $l ) {
						echo '<li><a href="' . esc_url( $l[1] ) . '">' . esc_html( $l[0] ) . '</a></li>';
					}
					echo '<li><a href="' . esc_url( kv_book_url() ) . '">Book a tour</a></li></ul>';
				}
				?>
			</div>
			<div>
				<h4>Policies</h4>
				<ul><?php foreach ( kv_policy_links() as $label => $url ) : ?><li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li><?php endforeach; ?></ul>
			</div>
		</div>
		<div class="foot-bottom"><span>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.</span><span><a href="<?php echo esc_url( kv_page_url( 'photo-credits' ) ); ?>">Photo credits</a></span></div>
	</div>
</footer>
<a class="wa-float" href="<?php echo esc_url( kv_whatsapp_url( 'Hello ' . get_bloginfo( 'name' ) . ', I would like to ask about a tour.' ) ); ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
	<svg viewBox="0 0 32 32" aria-hidden="true"><path d="M16.003 3C8.83 3 3 8.83 3 16c0 2.29.6 4.52 1.74 6.49L3 29l6.7-1.72A12.94 12.94 0 0 0 16 29c7.17 0 13-5.83 13-13S23.17 3 16.003 3Zm0 23.7c-2 0-3.96-.54-5.67-1.56l-.4-.24-3.98 1.02 1.06-3.88-.26-.4A10.66 10.66 0 0 1 5.3 16c0-5.9 4.8-10.7 10.7-10.7S26.7 10.1 26.7 16s-4.8 10.7-10.7 10.7Zm5.87-8c-.32-.16-1.9-.94-2.2-1.05-.3-.1-.5-.16-.72.16-.22.32-.84 1.05-1.03 1.27-.19.22-.38.24-.7.08-.32-.16-1.36-.5-2.6-1.6-.96-.86-1.6-1.9-1.8-2.22-.18-.32-.02-.5.14-.66.14-.14.32-.38.48-.57.16-.19.22-.32.32-.54.1-.22.05-.4-.03-.57-.08-.16-.72-1.74-.99-2.38-.26-.62-.52-.54-.72-.55h-.62c-.22 0-.57.08-.86.4-.3.32-1.13 1.1-1.13 2.7s1.16 3.13 1.32 3.35c.16.22 2.28 3.48 5.53 4.88.77.33 1.38.53 1.85.68.78.25 1.49.21 2.05.13.62-.09 1.9-.78 2.17-1.53.27-.75.27-1.4.19-1.53-.08-.13-.3-.21-.62-.37Z"/></svg>
</a>
<?php wp_footer(); ?>
</body>
</html>
