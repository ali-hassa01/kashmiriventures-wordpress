<?php
get_header();
$g = function ( $k ) {
	return isset( $_GET[ $k ] ) ? sanitize_text_field( wp_unslash( $_GET[ $k ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
};
$f = array( 'dest' => $g( 'dest' ), 'date' => $g( 'date' ), 'travellers' => (int) $g( 'travellers' ), 'type' => $g( 'type' ) );
if ( is_tax( 'kv_destination' ) ) {
	$f['dest'] = get_queried_object()->slug;
}
if ( is_tax( 'kv_tour_type' ) ) {
	$f['type'] = get_queried_object()->slug;
}
$tours = kv_search_tours( $f );
kv_page_head( is_tax() ? single_term_title( '', false ) : 'All Tour Packages', 'Choose a destination, check departure dates and book online or on WhatsApp.' );
?>
<section class="section" style="padding-top:48px">
	<div class="wrap">
		<div class="search-card" style="margin-bottom:34px"><?php kv_search_form( $f ); ?></div>
		<div class="filter-bar">
			<a class="pill<?php echo $f['type'] ? '' : ' on'; ?>" href="<?php echo esc_url( get_post_type_archive_link( 'kv_tour' ) ); ?>">All</a>
			<?php foreach ( (array) get_terms( array( 'taxonomy' => 'kv_tour_type', 'hide_empty' => false ) ) as $term ) : if ( is_wp_error( $term ) || ! is_object( $term ) ) { continue; } ?>
				<a class="pill<?php echo $f['type'] === $term->slug ? ' on' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'type', $term->slug, get_post_type_archive_link( 'kv_tour' ) ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
			<?php endforeach; ?>
		</div>
		<?php if ( $tours ) : ?>
			<p><?php echo (int) count( $tours ); ?> tour<?php echo 1 === count( $tours ) ? '' : 's'; ?> found.</p>
			<div class="tour-grid"><?php foreach ( $tours as $t ) { get_template_part( 'template-parts/tour-card', null, array( 'id' => $t->ID ) ); } ?></div>
		<?php else : ?>
			<div class="empty-note"><strong>No tours match your search.</strong> Try a different date or fewer travellers, or <a href="<?php echo esc_url( kv_book_url() ); ?>">send us a custom trip request</a>.</div>
		<?php endif; ?>
	</div>
</section>
<?php get_footer();
