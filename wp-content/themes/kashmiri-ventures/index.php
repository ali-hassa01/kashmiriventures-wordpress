<?php get_header(); ?>
<?php kv_page_head( is_search() ? 'Search results' : get_the_archive_title() ); ?>
<section class="section"><div class="wrap prose">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<article><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php the_excerpt(); ?></article>
<?php endwhile; else : ?><p>Nothing found.</p><?php endif; ?>
</div></section>
<?php get_footer();
