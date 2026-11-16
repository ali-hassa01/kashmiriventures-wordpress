<?php get_header(); kv_page_head( kv_opt( 'about_title' ) ); $img = (int) kv_opt( 'about_img_id' ); ?>
<section class="section"><div class="wrap split">
	<div class="prose"><?php foreach ( preg_split( '/\R{2,}/', trim( kv_opt( 'about_text' ) ) ) as $para ) { echo '<p>' . nl2br( esc_html( $para ) ) . '</p>'; } ?>
		<p><a class="btn" href="<?php echo esc_url( get_post_type_archive_link( 'kv_tour' ) ); ?>">See our tours</a></p></div>
	<?php echo $img ? wp_get_attachment_image( $img, 'large' ) : '<img class="ph" src="' . esc_attr( kv_placeholder_src( 3 ) ) . '" alt="">'; // phpcs:ignore ?>
</div></section>
<section class="section section-dark"><div class="wrap why">
	<div><h2><?php echo esc_html( kv_opt( 'why_title' ) ); ?></h2><p><?php echo esc_html( kv_opt( 'why_text' ) ); ?></p></div>
	<ul class="why-list"><?php foreach ( kv_lines( kv_opt( 'why_items' ) ) as $line ) : $p = explode( '|', $line, 2 ); ?>
		<li><h3><?php echo esc_html( trim( $p[0] ) ); ?></h3><?php if ( isset( $p[1] ) ) : ?><p><?php echo esc_html( trim( $p[1] ) ); ?></p><?php endif; ?></li>
	<?php endforeach; ?></ul>
</div></section>
<?php get_footer();
