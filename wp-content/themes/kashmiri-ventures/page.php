<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<?php kv_page_head( get_the_title() ); ?>
<section class="section"><div class="wrap prose"><?php the_content(); ?></div></section>
<?php endwhile; ?>
<?php get_footer();
