<?php
get_header();
while ( have_posts() ) : the_post();
	$id    = get_the_ID();
	$m     = function ( $k ) use ( $id ) {
		return get_post_meta( $id, $k, true );
	};
	$deps  = kv_get_departures( $id );
	$price = $m( '_kv_price' );
	$dur   = $m( '_kv_duration' );
	$dest  = kv_tour_destination( $id );
	$types = get_the_terms( $id, 'kv_tour_type' );
	$arr   = function ( $k ) use ( $id ) {
		$v = get_post_meta( $id, $k, true );
		return is_array( $v ) ? $v : array();
	};
	$itin  = $arr( '_kv_itinerary' );
	$faqs  = $arr( '_kv_faqs' );
	$gal   = $arr( '_kv_gallery' );
	$inc   = kv_lines( $m( '_kv_includes' ) );
	$exc   = kv_lines( $m( '_kv_excludes' ) );
	if ( ! $inc ) {
		$inc = kv_lines( kv_opt( 'inc_common' ) );
	}
	if ( ! $exc ) {
		$exc = kv_lines( kv_opt( 'exc_common' ) );
	}
	$pk    = kv_tour_packages( $id );
	$info  = kv_lines( $m( '_kv_info' ) );
	$credit = $m( '_kv_photo_credit' );
	$first = null;
	foreach ( $deps as $d ) {
		if ( $d['remaining'] > 0 ) {
			$first = $d;
			break;
		}
	}
	?>
<section class="tour-hero">
	<?php echo kv_tour_img( $id, 'kv-wide' ); // phpcs:ignore ?>
	<div class="wrap">
		<p class="crumbs"><a href="<?php echo esc_url( get_post_type_archive_link( 'kv_tour' ) ); ?>">All tours</a><?php echo $dest ? ' / ' . esc_html( $dest ) : ''; ?></p>
		<h1><?php the_title(); ?></h1>
		<div class="facts">
			<?php if ( $dest ) : ?><span class="fact"><?php echo esc_html( $dest ); ?></span><?php endif; ?>
			<?php if ( $dur ) : ?><span class="fact"><?php echo esc_html( $dur ); ?></span><?php endif; ?>
			<?php if ( $types && ! is_wp_error( $types ) ) : foreach ( $types as $t ) : ?><span class="fact"><?php echo esc_html( $t->name ); ?></span><?php endforeach; endif; ?>
		</div>
		<?php if ( $credit ) : ?><p class="photo-credit"><?php echo esc_html( $credit ); ?></p><?php endif; ?>
	</div>
</section>

<div class="wrap tour-layout">
	<div class="tour-main">
		<section><h2>Overview</h2><div class="prose prose-wide"><?php the_content(); ?></div></section>

		<?php if ( $pk ) : ?><section><h2>Package prices</h2><?php kv_package_table( $pk ); ?></section><?php endif; ?>

		<?php if ( $gal ) : ?>
		<section><h2>Photo gallery</h2><div class="gal">
			<?php foreach ( $gal as $gid ) : $full = wp_get_attachment_image_url( $gid, 'full' ); if ( ! $full ) { continue; } ?>
				<a href="<?php echo esc_url( $full ); ?>" target="_blank" rel="noopener"><?php echo wp_get_attachment_image( $gid, 'kv-card', false, array( 'loading' => 'lazy' ) ); // phpcs:ignore ?></a>
			<?php endforeach; ?>
		</div></section>
		<?php endif; ?>

		<?php if ( $itin ) : ?>
		<section><h2>Day-by-day itinerary</h2>
			<?php foreach ( $itin as $i => $day ) : ?>
				<details class="day"<?php echo 0 === $i ? ' open' : ''; ?>><summary><?php echo esc_html( $day['title'] ); ?></summary><div><?php echo nl2br( esc_html( $day['text'] ) ); ?></div></details>
			<?php endforeach; ?>
		</section>
		<?php endif; ?>

		<?php if ( $inc || $exc ) : ?>
		<section class="inc-exc">
			<div><h2>Included</h2><ul class="ticks"><?php foreach ( $inc as $l ) { echo '<li>' . esc_html( $l ) . '</li>'; } ?></ul></div>
			<div><h2>Not included</h2><ul class="crosses"><?php foreach ( $exc as $l ) { echo '<li>' . esc_html( $l ) . '</li>'; } ?></ul></div>
		</section>
		<?php endif; ?>

		<?php if ( $info ) : ?>
		<section><h2>Important travel information</h2><ul class="dots"><?php foreach ( $info as $l ) { echo '<li>' . esc_html( $l ) . '</li>'; } ?></ul></section>
		<?php endif; ?>

		<section><h2>Transport, food and pick and drop</h2><?php get_template_part( 'template-parts/travel-info', null, array( 'skip_inc' => true ) ); ?></section>

		<?php if ( $faqs ) : ?>
		<section class="faq"><h2>Questions about this tour</h2>
			<?php foreach ( $faqs as $f ) : ?>
				<details><summary><?php echo esc_html( $f['q'] ); ?></summary><div><?php echo nl2br( esc_html( $f['a'] ) ); ?></div></details>
			<?php endforeach; ?>
		</section>
		<?php endif; ?>
	</div>

	<aside>
		<div class="book-card" id="book-card"
			data-book="<?php echo esc_url( kv_book_url( $id ) ); ?>"
			data-wa="<?php echo esc_url( kv_whatsapp_url() ); ?>"
			data-title="<?php echo esc_attr( get_the_title() ); ?>">
			<div class="price"><?php echo kv_price_html( $id ); // phpcs:ignore ?></div>
			<?php if ( $deps ) : ?>
				<div class="field"><label for="kv-dep">Departure date</label>
					<select id="kv-dep">
						<?php foreach ( $deps as $d ) : ?>
							<option value="<?php echo esc_attr( $d['date'] ); ?>" data-label="<?php echo esc_attr( kv_date( $d['date'] ) ); ?>" <?php disabled( $d['remaining'] <= 0 ); ?> <?php selected( $first && $first['date'] === $d['date'] ); ?>>
								<?php echo esc_html( kv_date( $d['date'] ) . ' (' . ( $d['remaining'] > 0 ? $d['remaining'] . ' seats left' : 'fully booked' ) . ')' ); ?>
							</option>
						<?php endforeach; ?>
					</select></div>
			<?php else : ?>
				<p>This is a private or customized tour. Tell us your dates and group size in the booking form.</p>
			<?php endif; ?>
			<div class="field"><label for="kv-trav">Travellers</label><input id="kv-trav" type="number" min="1" max="50" value="2"></div>
			<a class="btn btn-gold btn-block" id="kv-book-link" href="<?php echo esc_url( kv_book_url( $id, $first ? $first['date'] : '' ) ); ?>">Book Now</a>
			<a class="btn btn-wa btn-block" id="kv-wa-link" target="_blank" rel="noopener" href="<?php echo esc_url( kv_whatsapp_url( 'Hello, I want to book: ' . get_the_title() ) ); ?>">Book on WhatsApp</a>
			<p style="margin:14px 0 0;font-size:.9rem;color:var(--muted)">Submitting a booking is a request. Your seats are confirmed after our team contacts you.</p>
		</div>
	</aside>
</div>
<?php endwhile; ?>
<?php get_footer();
