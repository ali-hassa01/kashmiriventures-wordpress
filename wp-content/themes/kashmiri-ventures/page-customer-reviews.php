<?php get_header(); kv_page_head( 'Customer Reviews', 'What travellers say about their trips with us.' );
$reviews = get_posts( array( 'post_type' => 'kv_review', 'post_status' => 'publish', 'posts_per_page' => -1 ) ); ?>
<section class="section"><div class="wrap">
<?php if ( $reviews ) : ?><div class="review-grid"><?php foreach ( $reviews as $r ) { get_template_part( 'template-parts/review', null, array( 'id' => $r->ID ) ); } ?></div>
<?php else : ?><div class="empty-note">No reviews yet. Add them in wp-admin under Kashmiri Ventures, then Reviews.</div><?php endif; ?>
</div></section>
<?php get_footer();
