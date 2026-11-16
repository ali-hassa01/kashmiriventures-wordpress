<?php get_header(); ?>
<?php kv_page_head( 'Page not found', 'The page you are looking for does not exist or has moved.' ); ?>
<section class="section"><div class="wrap"><a class="btn" href="<?php echo esc_url( get_post_type_archive_link( 'kv_tour' ) ); ?>">Browse tours</a></div></section>
<?php get_footer();
