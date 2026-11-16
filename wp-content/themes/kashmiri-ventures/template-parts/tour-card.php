<?php
$id    = isset( $args['id'] ) ? (int) $args['id'] : get_the_ID();
$next  = kv_next_departure( $id );
$dest  = kv_tour_destination( $id );
$dur   = get_post_meta( $id, '_kv_duration', true );
$price = get_post_meta( $id, '_kv_price', true );
?>
<article class="tour-card">
	<a class="tour-card-img" href="<?php echo esc_url( get_permalink( $id ) ); ?>" tabindex="-1" aria-hidden="true">
		<?php echo kv_tour_img( $id, 'kv-card' ); // phpcs:ignore ?>
		<?php if ( $dest ) : ?><span class="chip"><?php echo esc_html( $dest ); ?></span><?php endif; ?>
	</a>
	<div class="tour-card-body">
		<?php if ( $dur ) : ?><p class="tour-meta"><span><?php echo esc_html( $dur ); ?></span></p><?php endif; ?>
		<h3><a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo esc_html( get_the_title( $id ) ); ?></a></h3>
		<p class="tour-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $id ), 22 ) ); ?></p>
		<?php if ( $next ) : $st = kv_seat_status( $next['remaining'] ); ?>
			<div class="tour-next">
				<span>Next departure: <strong><?php echo esc_html( kv_date( $next['date'] ) ); ?></strong></span>
				<span class="seat seat-<?php echo esc_attr( $st[0] ); ?>"><?php echo $next['remaining'] > 0 ? (int) $next['remaining'] . ' seats available' : 'Fully booked'; ?></span>
			</div>
		<?php else : ?>
			<div class="tour-next"><span>Dates on request</span><span class="seat seat-open">Private or customized</span></div>
		<?php endif; ?>
		<div class="tour-card-foot">
			<div class="price"><?php echo kv_price_html( $id ); // phpcs:ignore ?></div>
			<div class="btns">
				<a class="btn btn-line btn-sm" href="<?php echo esc_url( get_permalink( $id ) ); ?>">View Details</a>
				<a class="btn btn-sm" href="<?php echo esc_url( kv_book_url( $id, $next ? $next['date'] : '' ) ); ?>">Book Now</a>
			</div>
		</div>
	</div>
</article>
