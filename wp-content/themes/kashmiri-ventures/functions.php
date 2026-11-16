<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'KV_THEME_VERSION', '1.0.0' );

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_image_size( 'kv-card', 640, 440, true );
	add_image_size( 'kv-wide', 1600, 760, true );
	register_nav_menus( array(
		'primary' => 'Primary menu (optional)',
		'footer'  => 'Footer quick links (optional)',
	) );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'kv-fonts', 'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Figtree:wght@400;500;600;700&display=swap', array(), null );
	wp_enqueue_style( 'kv-style', get_stylesheet_uri(), array( 'kv-fonts' ), KV_THEME_VERSION );
	wp_enqueue_script( 'kv-main', get_template_directory_uri() . '/assets/js/main.js', array(), KV_THEME_VERSION, true );
} );

/* The theme needs the Kashmiri Ventures Core plugin. Without it, show a clear message instead of a fatal error. */
if ( ! defined( 'KV_VERSION' ) ) {
	add_action( 'admin_notices', function () {
		echo '<div class="notice notice-error"><p><strong>Kashmiri Ventures theme:</strong> please activate the "Kashmiri Ventures Core" plugin (Plugins menu).</p></div>';
	} );
	add_filter( 'template_include', function () {
		return get_template_directory() . '/plugin-missing.php';
	} );
	return;
}

function kv_nav_links() {
	return array(
		array( 'Home', home_url( '/' ) ),
		array( 'Tours', get_post_type_archive_link( 'kv_tour' ) ),
		array( 'About Us', kv_page_url( 'about' ) ),
		array( 'Gallery', kv_page_url( 'gallery' ) ),
		array( 'Reviews', kv_page_url( 'customer-reviews' ) ),
		array( 'FAQs', kv_page_url( 'faqs' ) ),
		array( 'Contact', kv_page_url( 'contact' ) ),
	);
}

function kv_primary_nav() {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'nav-list' ) );
		return;
	}
	$li        = function ( $label, $url ) {
		return '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	};
	$tours_url = get_post_type_archive_link( 'kv_tour' );
	$dests     = get_terms( array( 'taxonomy' => 'kv_destination', 'hide_empty' => true ) );
	echo '<ul class="nav-list">';
	echo $li( 'Home', home_url( '/' ) ); // phpcs:ignore
	echo '<li class="has-sub"><a href="' . esc_url( $tours_url ) . '">Tours</a><ul class="sub">';
	echo $li( 'All Tours', $tours_url ); // phpcs:ignore
	if ( ! is_wp_error( $dests ) ) {
		foreach ( $dests as $d ) {
			$link = get_term_link( $d );
			if ( ! is_wp_error( $link ) ) {
				echo $li( $d->name, $link ); // phpcs:ignore
			}
		}
	}
	$couple = get_term_by( 'slug', 'couple-trip', 'kv_tour_type' );
	if ( $couple ) {
		$link = get_term_link( $couple );
		if ( ! is_wp_error( $link ) ) {
			echo $li( 'Couple Trips', $link ); // phpcs:ignore
		}
	}
	echo '</ul></li>';
	echo '<li class="has-sub"><a href="' . esc_url( kv_page_url( 'about' ) ) . '">About Us</a><ul class="sub">';
	echo $li( 'About Us', kv_page_url( 'about' ) ); // phpcs:ignore
	echo $li( 'FAQs', kv_page_url( 'faqs' ) ); // phpcs:ignore
	foreach ( kv_policy_links() as $label => $url ) {
		echo $li( $label, $url ); // phpcs:ignore
	}
	echo '</ul></li>';
	if ( (int) wp_count_posts( 'kv_gallery' )->publish > 0 ) {
		echo $li( 'Gallery', kv_page_url( 'gallery' ) ); // phpcs:ignore
	}
	if ( (int) wp_count_posts( 'kv_review' )->publish > 0 ) {
		echo $li( 'Reviews', kv_page_url( 'customer-reviews' ) ); // phpcs:ignore
	}
	echo $li( 'Contact', kv_page_url( 'contact' ) ); // phpcs:ignore
	echo '</ul>';
}

function kv_policy_links() {
	return array(
		'Booking Policies'     => kv_page_url( 'booking-policy' ),
		'Cancellation Policy'  => kv_page_url( 'cancellation-policy' ),
		'Refund Policy'        => kv_page_url( 'refund-policy' ),
		'Privacy Policy'       => kv_page_url( 'privacy-policy' ),
		'Terms and Conditions' => kv_page_url( 'terms-and-conditions' ),
	);
}

function kv_social_links() {
	$out = array();
	foreach ( array( 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'tiktok' => 'TikTok' ) as $k => $label ) {
		$u = kv_opt( $k );
		if ( $u ) {
			$out[ $label ] = $u;
		}
	}
	return $out;
}

function kv_social_icon( $label ) {
	$icons = array(
		'Instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
		'TikTok'    => '<svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M16.6 3c.3 2.3 1.8 3.9 4 4.1v3.1c-1.4 0-2.8-.4-4-1.2v6.2a5.6 5.6 0 1 1-5.6-5.6c.3 0 .6 0 .9.1v3.2a2.5 2.5 0 1 0 1.6 2.3V3h3.1Z"/></svg>',
		'Facebook'  => '<svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M14 8.5V6.8c0-.7.2-1.1 1.2-1.1H17V2.5h-2.6c-2.7 0-3.9 1.5-3.9 4v2H8v3.2h2.5v9.8H14v-9.8h2.7l.4-3.2H14Z"/></svg>',
		'YouTube'   => '<svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8ZM10 15V9l5.200 3L10 15Z"/></svg>',
	);
	return isset( $icons[ $label ] ) ? $icons[ $label ] : '';
}

function kv_package_table( $pk ) {
	$rooms = kv_room_types();
	echo '<div class="pkg-wrap"><table class="pkg"><thead><tr><th></th>';
	foreach ( $rooms as $l ) {
		echo '<th>' . esc_html( $l ) . '</th>';
	}
	echo '</tr></thead><tbody>';
	foreach ( kv_package_tiers() as $t => $tl ) {
		if ( ! isset( $pk[ $t ] ) ) {
			continue;
		}
		echo '<tr><th scope="row">' . esc_html( $tl ) . '</th>';
		foreach ( $rooms as $r => $l ) {
			$v = (int) $pk[ $t ][ $r ];
			echo '<td>' . ( $v > 0 ? kv_money( $v ) : '-' ) . '</td>'; // phpcs:ignore
		}
		echo '</tr>';
	}
	echo '</tbody></table></div><p class="pkg-note"><strong>Solo:</strong> one person sharing a room with 4 to 5 persons. <strong>Couple:</strong> price for 2 persons in a separate room. <strong>Single:</strong> one person in a separate room.</p>';
}

function kv_stars( $n ) {
	$n = max( 0, min( 5, (int) $n ) );
	return '<span class="stars" role="img" aria-label="' . esc_attr( $n ) . ' out of 5 stars">' . str_repeat( '&#9733;', $n ) . str_repeat( '&#9734;', 5 - $n ) . '</span>';
}

function kv_hero_src() {
	$id = (int) kv_opt( 'hero_id' );
	if ( $id ) {
		$u = wp_get_attachment_image_url( $id, 'full' );
		if ( $u ) {
			return $u;
		}
	}
	return get_theme_file_uri( 'assets/img/hero-fairy-meadows.svg' );
}

function kv_page_head( $title, $text = '' ) {
	echo '<section class="page-head"><div class="wrap"><h1>' . esc_html( $title ) . '</h1>';
	if ( $text ) {
		echo '<p>' . esc_html( $text ) . '</p>';
	}
	echo '</div></section>';
}

function kv_search_form( $values = array() ) {
	$v = wp_parse_args( $values, array( 'dest' => '', 'date' => '', 'travellers' => '' ) );
	$terms = get_terms( array( 'taxonomy' => 'kv_destination', 'hide_empty' => false ) );
	?>
	<form class="search-form" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'kv_tour' ) ); ?>">
		<div class="field"><label for="s-dest">Destination</label>
			<select id="s-dest" name="dest"><option value="">Any destination</option>
				<?php if ( ! is_wp_error( $terms ) ) : foreach ( $terms as $t ) : ?>
					<option value="<?php echo esc_attr( $t->slug ); ?>" <?php selected( $v['dest'], $t->slug ); ?>><?php echo esc_html( $t->name ); ?></option>
				<?php endforeach; endif; ?>
			</select></div>
		<div class="field"><label for="s-date">Travelling from</label>
			<input id="s-date" type="date" name="date" min="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" value="<?php echo esc_attr( $v['date'] ); ?>"></div>
		<div class="field"><label for="s-trav">Travellers</label>
			<select id="s-trav" name="travellers"><option value="">Any number</option>
				<?php for ( $i = 1; $i <= 20; $i++ ) : ?>
					<option value="<?php echo (int) $i; ?>" <?php selected( (int) $v['travellers'], $i ); ?>><?php echo (int) $i; ?></option>
				<?php endfor; ?>
			</select></div>
		<button class="btn btn-gold" type="submit">Search tours</button>
	</form>
	<?php
}
