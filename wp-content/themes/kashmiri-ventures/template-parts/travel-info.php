<?php
$skip   = ! empty( $args['skip_inc'] );
$cities = array();
foreach ( kv_lines( kv_opt( 'cities' ) ) as $l ) {
	$p        = explode( '|', $l, 2 );
	$cities[] = array( trim( $p[0] ), isset( $p[1] ) ? trim( $p[1] ) : '' );
}
?>
<div class="info-grid">
	<article class="info-card"><h3>Transportation</h3><p><?php echo esc_html( kv_opt( 'transport_text' ) ); ?></p></article>
	<article class="info-card"><h3>Food menu</h3>
		<p><strong>Breakfast:</strong> <?php echo esc_html( kv_opt( 'food_breakfast' ) ); ?></p>
		<p><strong>Dinner:</strong> <?php echo esc_html( kv_opt( 'food_dinner' ) ); ?></p>
		<?php if ( kv_opt( 'food_note' ) ) : ?><p class="info-note"><?php echo esc_html( kv_opt( 'food_note' ) ); ?></p><?php endif; ?>
	</article>
	<article class="info-card info-wide"><h3>Departure cities, pick and drop</h3>
		<ul class="cities"><?php foreach ( $cities as $c ) : ?><li><strong><?php echo esc_html( $c[0] ); ?></strong><span><?php echo esc_html( $c[1] ); ?></span></li><?php endforeach; ?></ul>
		<ul class="dots"><?php foreach ( kv_lines( kv_opt( 'cities_notes' ) ) as $n ) { echo '<li>' . esc_html( $n ) . '</li>'; } ?></ul>
	</article>
	<?php if ( ! $skip ) : ?>
	<article class="info-card"><h3>Services included</h3><ul class="ticks"><?php foreach ( kv_lines( kv_opt( 'inc_common' ) ) as $n ) { echo '<li>' . esc_html( $n ) . '</li>'; } ?></ul></article>
	<article class="info-card"><h3>Services not included</h3><ul class="crosses"><?php foreach ( kv_lines( kv_opt( 'exc_common' ) ) as $n ) { echo '<li>' . esc_html( $n ) . '</li>'; } ?></ul></article>
	<?php endif; ?>
</div>
