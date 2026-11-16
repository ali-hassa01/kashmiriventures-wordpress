<?php
$r     = $args['r'];
$i     = (int) $args['i'];
$tid   = (int) $r['tour_id'];
$tour  = ( $tid && 'publish' === get_post_status( $tid ) ) ? $tid : 0;
$pk    = $tour ? kv_tour_packages( $tour ) : array();
if ( ! $pk ) {
	$pk = kv_clean_packages( (array) $r['prices'] );
}
$title = ! empty( $r['heading'] ) ? $r['heading'] : $r['name'];
$img   = ! empty( $r['image_id'] ) ? wp_get_attachment_image( (int) $r['image_id'], 'large', false, array( 'loading' => 'lazy' ) ) : '';
if ( ! $img ) {
	$k = kv_photo_key_for_text( $r['name'] . ' ' . $title );
	if ( $k ) {
		$img = '<img src="' . esc_url( kv_photo_url( $k, 1000 ) ) . '" alt="' . esc_attr( $title ) . '" loading="lazy">';
	} else {
		$img = '<img src="' . esc_attr( kv_placeholder_src( $i + 1 ) ) . '" alt="" loading="lazy">';
	}
}
?>
<article class="dest-row<?php echo $i % 2 ? ' rev' : ''; ?>">
	<div class="dest-img"><?php echo $img; // phpcs:ignore ?></div>
	<div class="dest-body">
		<h3><?php echo esc_html( $title ); ?></h3>
		<p><?php echo nl2br( esc_html( $r['text'] ) ); ?></p>
		<?php if ( $pk ) { kv_package_table( $pk ); } else { echo '<p class="price-ask">Price on request</p>'; } ?>
		<div class="btns">
			<?php if ( $tour ) : ?>
				<a class="btn btn-line btn-sm" href="<?php echo esc_url( get_permalink( $tour ) ); ?>">View tour</a>
				<a class="btn btn-sm" href="<?php echo esc_url( kv_book_url( $tour ) ); ?>">Book Now</a>
			<?php else : ?>
				<a class="btn btn-sm" href="<?php echo esc_url( kv_book_url() ); ?>">Plan this trip</a>
			<?php endif; ?>
		</div>
	</div>
</article>
