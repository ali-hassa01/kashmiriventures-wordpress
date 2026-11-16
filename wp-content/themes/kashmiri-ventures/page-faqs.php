<?php get_header(); kv_page_head( 'Frequently Asked Questions' );
$faqs = get_posts( array( 'post_type' => 'kv_faq', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC' ) ); ?>
<section class="section"><div class="wrap prose faq">
<?php if ( $faqs ) : foreach ( $faqs as $f ) : ?>
	<details><summary><?php echo esc_html( $f->post_title ); ?></summary><div><?php echo wp_kses_post( wpautop( $f->post_content ) ); ?></div></details>
<?php endforeach; else : ?><div class="empty-note">No FAQs yet. Add them in wp-admin under Kashmiri Ventures, then FAQs.</div><?php endif; ?>
</div></section>
<?php get_footer();
