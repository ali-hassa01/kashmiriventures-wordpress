<?php
/**
 * Plugin Name: Kashmiri Ventures Core
 * Description: Tours, departures, bookings, customers, payment records and website settings for the Kashmiri Ventures website. Required by the "Kashmiri Ventures" theme.
 * Version: 1.0.5
 * Author: Kashmiri Ventures
 * Text Domain: kv-core
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'KV_VERSION', '1.0.5' );
define( 'KV_PATH', plugin_dir_path( __FILE__ ) );
define( 'KV_URL', plugin_dir_url( __FILE__ ) );

require_once KV_PATH . 'includes/helpers.php';
require_once KV_PATH . 'includes/post-types.php';
require_once KV_PATH . 'includes/images.php';
require_once KV_PATH . 'includes/metaboxes.php';
require_once KV_PATH . 'includes/install.php';
require_once KV_PATH . 'includes/bookings.php';
require_once KV_PATH . 'includes/admin.php';
require_once KV_PATH . 'includes/demo.php';
require_once KV_PATH . 'includes/real-content.php';

register_activation_hook( __FILE__, 'kv_activate' );
