<?php get_header(); kv_page_head( 'Photo Gallery', 'Moments from the mountains, lakes and valleys we travel through.' );
$items = get_posts( array( 'post_type' => 'kv_gallery', 'post_status' => 'publish', 'posts_per_page' => -1 ) ); ?>
<section class="section"><div class="wrap">
<?php if ( $items ) : ?><div class="gallery-grid"><?php foreach ( $items as $i ) : ?>
	<figure><?php echo has_post_thumbnail( $i->ID ) ? get_the_post_thumbnail( $i->ID, 'large', array( 'loading' => 'lazy' ) ) : '<img src="' . esc_attr( kv_placeholder_src( $i->ID ) ) . '" alt="" loading="lazy">'; // phpcs:ignore ?><figcaption><?php echo esc_html( $i->post_title ); ?></figcaption></figure>
<?php endforeach; ?></div>
<?php else : ?><div class="empty-note">No photos yet. Add them in wp-admin under Kashmiri Ventures, then Gallery.</div><?php endif; ?>
</div></section>
<?php get_footer();
