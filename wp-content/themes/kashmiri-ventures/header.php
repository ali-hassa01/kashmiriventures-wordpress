<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">Skip to content</a>
<div class="topbar"><div class="wrap">
	<div class="tb-l">
		<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', kv_opt( 'phone' ) ) ); ?>"><?php echo esc_html( kv_opt( 'phone' ) ); ?></a>
		<a href="mailto:<?php echo esc_attr( kv_opt( 'email' ) ); ?>"><?php echo esc_html( kv_opt( 'email' ) ); ?></a>
	</div>
	<div class="tb-r"><?php foreach ( kv_social_links() as $label => $url ) : ?><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $label ); ?></a><?php endforeach; ?></div>
</div></div>
<header class="site-header">
	<div class="wrap header-in">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?> home"><?php echo kv_logo_html(); // phpcs:ignore ?></a>
		<button class="nav-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="main-nav"><span></span><span></span><span></span></button>
		<nav class="main-nav" id="main-nav" aria-label="Main"><?php kv_primary_nav(); ?></nav>
		<div class="header-cta"><a class="btn btn-gold btn-sm" href="<?php echo esc_url( kv_book_url() ); ?>">Book Now</a></div>
	</div>
</header>
<main id="main">
