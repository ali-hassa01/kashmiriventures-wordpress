<?php get_header(); kv_page_head( 'Photo Credits', 'Destination photos used on this website.' ); ?>
<section class="section"><div class="wrap prose">
	<p>Destination photos are from Wikimedia Commons, where every file is freely licensed. Each link below opens the photo page with the photographer, the exact licence and its terms.</p>
	<ul>
	<?php foreach ( kv_photo_catalog() as $key => $c ) : ?>
		<li><?php echo esc_html( $c[1] ); ?>: <a href="<?php echo esc_url( kv_photo_page( $key ) ); ?>" target="_blank" rel="noopener">photo page on Wikimedia Commons</a></li>
	<?php endforeach; ?>
	</ul>
	<p>Replacing these with your own photos: in wp-admin, add a Featured Image to each tour and set the homepage banner in Settings.</p>
</div></section>
<?php get_footer();
