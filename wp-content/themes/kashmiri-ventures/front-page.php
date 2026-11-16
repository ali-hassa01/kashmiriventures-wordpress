<?php get_header(); ?>

<?php
$hero_id = (int) kv_opt( 'hero_id' );
$slides  = array();
if ( $hero_id && wp_get_attachment_image_url( $hero_id, 'full' ) ) {
	$slides[] = array( wp_get_attachment_image_url( $hero_id, 'full' ), get_bloginfo( 'name' ) );
} else {
	$cat = kv_photo_catalog();
	foreach ( array( 'hunza', 'fairy', 'deosai', 'naran', 'neelum' ) as $k ) {
		$slides[] = array( kv_photo_url( $k, 1920 ), $cat[ $k ][1] );
	}
}
?>
<section class="hero">
	<div class="hero-slides" aria-hidden="true">
		<?php foreach ( $slides as $i => $sl ) : ?>
			<div class="hero-slide<?php echo 0 === $i ? ' on' : ''; ?>" data-cap="<?php echo esc_attr( $sl[1] ); ?>" style="background-image:url('<?php echo esc_url( $sl[0] ); ?>')"></div>
		<?php endforeach; ?>
	</div>
	<div class="wrap hero-main">
		<h1><?php echo esc_html( kv_opt( 'hero_title' ) ); ?></h1>
		<p><?php echo esc_html( kv_opt( 'hero_text' ) ); ?></p>
		<div class="hero-btns">
			<a class="btn btn-gold" href="<?php echo esc_url( get_post_type_archive_link( 'kv_tour' ) ); ?>"><?php echo esc_html( kv_opt( 'hero_btn1' ) ); ?></a>
			<a class="btn btn-ghost" href="<?php echo esc_url( kv_book_url() ); ?>"><?php echo esc_html( kv_opt( 'hero_btn2' ) ); ?></a>
		</div>
	</div>
	<div class="wrap hero-search">
		<div class="search-card">
			<h2><?php echo esc_html( kv_opt( 'search_title' ) ); ?></h2>
			<?php kv_search_form(); ?>
		</div>
		<?php if ( count( $slides ) > 1 ) : ?><p class="hero-cap" id="hero-cap"><?php echo esc_html( $slides[0][1] ); ?></p><?php endif; ?>
	</div>
</section>

<div class="strip"><div class="wrap">
	<?php foreach ( kv_lines( kv_opt( 'strip_items' ) ) as $line ) : $p = explode( '|', $line, 2 ); ?>
		<div class="strip-item"><strong><?php echo esc_html( trim( $p[0] ) ); ?></strong><span><?php echo isset( $p[1] ) ? esc_html( trim( $p[1] ) ) : ''; ?></span></div>
	<?php endforeach; ?>
</div></div>

<section class="section" id="tours">
	<div class="wrap">
		<div class="section-head"><h2><?php echo esc_html( kv_opt( 'popular_title' ) ); ?></h2><p><?php echo esc_html( kv_opt( 'popular_text' ) ); ?></p></div>
		<?php
		$tours = kv_search_tours( array( 'featured' => true, 'limit' => 6 ) );
		if ( ! $tours ) {
			$tours = kv_search_tours( array( 'limit' => 6 ) );
		}
		if ( $tours ) : ?>
			<div class="tour-grid">
				<?php foreach ( $tours as $t ) { get_template_part( 'template-parts/tour-card', null, array( 'id' => $t->ID ) ); } ?>
			</div>
			<p style="margin-top:34px"><a class="btn btn-line" href="<?php echo esc_url( get_post_type_archive_link( 'kv_tour' ) ); ?>">See all tours</a></p>
		<?php else : ?>
			<div class="empty-note">
				<strong>No tours yet.</strong>
				<?php if ( current_user_can( 'manage_options' ) ) : ?>
					In wp-admin open <em>Kashmiri Ventures, Tours, Add New</em>, or <a href="<?php echo esc_url( admin_url( 'admin.php?page=kv-settings' ) ); ?>">load the demo content</a>.
				<?php else : ?>
					New tour packages are coming soon.
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
$secs = kv_opt( 'dest_sections' );
$secs = is_array( $secs ) ? array_values( array_filter( $secs, function ( $r ) {
	return ! empty( $r['show'] );
} ) ) : array();
if ( $secs ) : ?>
<section class="section section-tint" id="destinations">
	<div class="wrap">
		<div class="section-head"><h2><?php echo esc_html( kv_opt( 'dest_title' ) ); ?></h2><p><?php echo esc_html( kv_opt( 'dest_text' ) ); ?></p></div>
		<?php foreach ( $secs as $i => $r ) { get_template_part( 'template-parts/dest-section', null, array( 'r' => $r, 'i' => $i ) ); } ?>
	</div>
</section>
<?php endif; ?>

<section class="section section-dark">
	<div class="wrap why">
		<div><h2><?php echo esc_html( kv_opt( 'why_title' ) ); ?></h2><p><?php echo esc_html( kv_opt( 'why_text' ) ); ?></p>
			<?php if ( kv_opt( 'stat_clients' ) ) : ?><p class="stat"><strong><?php echo esc_html( kv_opt( 'stat_clients' ) ); ?></strong> Happy Clients</p><?php endif; ?>
			<a class="btn btn-gold" href="<?php echo esc_url( kv_page_url( 'about' ) ); ?>">About us</a></div>
		<ul class="why-list">
			<?php foreach ( kv_lines( kv_opt( 'why_items' ) ) as $line ) :
				$p = explode( '|', $line, 2 ); ?>
				<li><h3><?php echo esc_html( trim( $p[0] ) ); ?></h3><?php if ( isset( $p[1] ) ) : ?><p><?php echo esc_html( trim( $p[1] ) ); ?></p><?php endif; ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<div class="section-head"><h2><?php echo esc_html( kv_opt( 'info_title' ) ); ?></h2></div>
		<?php get_template_part( 'template-parts/travel-info' ); ?>
	</div>
</section>

<?php
/* Upcoming departures across all published tours */
$rows = array();
foreach ( kv_search_tours() as $t ) {
	foreach ( kv_get_departures( $t->ID ) as $d ) {
		$rows[] = array( 'tour' => $t, 'dep' => $d );
	}
}
usort( $rows, function ( $a, $b ) {
	return strcmp( $a['dep']['date'], $b['dep']['date'] );
} );
$rows = array_slice( $rows, 0, 8 );
?>
<section class="section section-tint" id="departures">
	<div class="wrap">
		<div class="section-head"><h2><?php echo esc_html( kv_opt( 'departures_title' ) ); ?></h2></div>
		<?php if ( $rows ) : ?>
		<div class="board" role="table" aria-label="Upcoming departures">
			<div class="board-row board-head" role="row"><span>Date</span><span>Tour</span><span class="b-dur">Duration</span><span class="b-price">Package price</span><span>Availability</span><span></span></div>
			<?php foreach ( $rows as $r ) :
				$t  = $r['tour'];
				$d  = $r['dep'];
				$st = kv_seat_status( $d['remaining'] );
				$ts = strtotime( $d['date'] . ' 12:00:00' ); ?>
				<div class="board-row" role="row">
					<div class="b-date"><strong><?php echo esc_html( wp_date( 'j', $ts ) ); ?></strong><span><?php echo esc_html( wp_date( 'M Y', $ts ) ); ?></span></div>
					<div class="b-tour"><a href="<?php echo esc_url( get_permalink( $t ) ); ?>"><?php echo esc_html( $t->post_title ); ?></a><small><?php echo esc_html( kv_tour_destination( $t->ID ) ); ?></small></div>
					<div class="b-dur"><?php echo esc_html( get_post_meta( $t->ID, '_kv_duration', true ) ); ?></div>
					<div class="b-price"><?php echo kv_price_short( $t->ID ); // phpcs:ignore ?></div>
					<div class="b-status"><span class="status status-<?php echo esc_attr( $st[0] ); ?>"><?php echo esc_html( $st[1] ); ?></span><small style="display:block;color:#a9b8af"><?php echo (int) $d['remaining']; ?> of <?php echo (int) $d['seats']; ?> seats left</small></div>
					<div><?php if ( $d['remaining'] > 0 ) : ?><a class="btn btn-gold btn-sm" href="<?php echo esc_url( kv_book_url( $t->ID, $d['date'] ) ); ?>">Book Now</a><?php else : ?><a class="btn btn-ghost btn-sm" href="<?php echo esc_url( kv_whatsapp_url( 'Hello, ' . $t->post_title . ' on ' . kv_date( $d['date'] ) . ' is fully booked. Is there a waiting list?' ) ); ?>" target="_blank" rel="noopener">Ask on WhatsApp</a><?php endif; ?></div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php else : ?>
			<div class="empty-note">No upcoming group departures are listed right now. <a href="<?php echo esc_url( kv_book_url() ); ?>">Send us a custom trip request</a>.</div>
		<?php endif; ?>
	</div>
</section>

<?php
$reviews = get_posts( array( 'post_type' => 'kv_review', 'post_status' => 'publish', 'posts_per_page' => 3, 'orderby' => 'date', 'order' => 'DESC' ) );
if ( $reviews ) : ?>
<section class="section">
	<div class="wrap">
		<div class="section-head"><h2><?php echo esc_html( kv_opt( 'reviews_title' ) ); ?></h2></div>
		<div class="review-grid"><?php foreach ( $reviews as $r ) { get_template_part( 'template-parts/review', null, array( 'id' => $r->ID ) ); } ?></div>
		<p style="margin-top:30px"><a class="btn btn-line" href="<?php echo esc_url( kv_page_url( 'customer-reviews' ) ); ?>">Read all reviews</a></p>
	</div>
</section>
<?php endif; ?>

<section class="section section-tint">
	<div class="wrap">
		<div class="section-head"><h2><?php echo esc_html( kv_opt( 'how_title' ) ); ?></h2></div>
		<ol class="steps">
			<li><h3>Select Your Tour</h3><p>Browse the packages and pick a destination and departure date.</p></li>
			<li><h3>Submit Booking</h3><p>Send the short booking form, or message us on WhatsApp.</p></li>
			<li><h3>Confirm Details</h3><p>Our team calls you to confirm the plan, the price and the advance payment.</p></li>
			<li><h3>Enjoy Your Trip</h3><p>Pack your bags. We take care of the road, the hotels and the schedule.</p></li>
		</ol>
	</div>
</section>

<section class="section cta">
	<div class="wrap">
		<div><h2><?php echo esc_html( kv_opt( 'cta_title' ) ); ?></h2><p><?php echo esc_html( kv_opt( 'cta_text' ) ); ?></p></div>
		<a class="btn btn-gold" href="<?php echo esc_url( kv_book_url() ); ?>">Book Your Tour</a>
	</div>
</section>

<?php get_footer();
