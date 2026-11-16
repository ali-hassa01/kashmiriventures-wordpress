<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'kv_register_post_types' );
function kv_register_post_types() {
	register_post_type( 'kv_tour', array(
		'labels'       => array(
			'name'          => 'Tours',
			'singular_name' => 'Tour',
			'add_new'       => 'Add New Tour',
			'add_new_item'  => 'Add New Tour',
			'edit_item'     => 'Edit Tour',
			'all_items'     => 'All Tours',
			'search_items'  => 'Search Tours',
			'not_found'     => 'No tours found',
		),
		'public'       => true,
		'has_archive'  => 'tours',
		'rewrite'      => array( 'slug' => 'tour' ),
		'show_in_menu' => 'kv-dashboard',
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
	) );

	register_post_type( 'kv_review', array(
		'labels'       => array(
			'name'          => 'Reviews',
			'singular_name' => 'Review',
			'add_new_item'  => 'Add New Review',
			'edit_item'     => 'Edit Review',
			'all_items'     => 'Reviews',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => 'kv-dashboard',
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
	) );

	register_post_type( 'kv_gallery', array(
		'labels'       => array(
			'name'          => 'Gallery',
			'singular_name' => 'Gallery photo',
			'add_new_item'  => 'Add Gallery Photo',
			'edit_item'     => 'Edit Gallery Photo',
			'all_items'     => 'Gallery',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => 'kv-dashboard',
		'supports'     => array( 'title', 'thumbnail' ),
	) );

	register_post_type( 'kv_faq', array(
		'labels'       => array(
			'name'          => 'FAQs',
			'singular_name' => 'FAQ',
			'add_new_item'  => 'Add New FAQ',
			'edit_item'     => 'Edit FAQ',
			'all_items'     => 'FAQs',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => 'kv-dashboard',
		'supports'     => array( 'title', 'editor', 'page-attributes' ),
	) );

	register_taxonomy( 'kv_destination', 'kv_tour', array(
		'labels'            => array( 'name' => 'Destinations', 'singular_name' => 'Destination', 'menu_name' => 'Destinations', 'all_items' => 'All Destinations', 'add_new_item' => 'Add New Destination' ),
		'hierarchical'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'destination' ),
	) );

	register_taxonomy( 'kv_tour_type', 'kv_tour', array(
		'labels'            => array( 'name' => 'Tour Types', 'singular_name' => 'Tour Type', 'menu_name' => 'Tour Types', 'all_items' => 'All Tour Types', 'add_new_item' => 'Add New Tour Type' ),
		'hierarchical'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'tour-type' ),
	) );
}

/* Classic editor for our post types, so the custom boxes sit with the content. */
add_filter( 'use_block_editor_for_post_type', function ( $use, $type ) {
	return in_array( $type, array( 'kv_tour', 'kv_review', 'kv_gallery', 'kv_faq' ), true ) ? false : $use;
}, 10, 2 );
