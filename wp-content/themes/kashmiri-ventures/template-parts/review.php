<?php
$id   = (int) $args['id'];
$name = get_the_title( $id );
$loc  = get_post_meta( $id, '_kv_location', true );
$rate = (int) get_post_meta( $id, '_kv_rating', true );
?>
<article class="review">
	<?php echo kv_stars( $rate ? $rate : 5 ); // phpcs:ignore ?>
	<blockquote><?php echo esc_html( wp_strip_all_tags( get_post_field( 'post_content', $id ) ) ); ?></blockquote>
	<div class="who">
		<span class="avatar"><?php echo has_post_thumbnail( $id ) ? get_the_post_thumbnail( $id, 'thumbnail' ) : esc_html( mb_strtoupper( mb_substr( $name, 0, 1 ) ) ); // phpcs:ignore ?></span>
		<div><strong><?php echo esc_html( $name ); ?></strong><?php if ( $loc ) : ?><small><?php echo esc_html( $loc ); ?></small><?php endif; ?></div>
	</div>
</article>
