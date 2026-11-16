<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes', 'kv_add_meta_boxes' );
function kv_add_meta_boxes() {
	add_meta_box( 'kv_tour_details', 'Tour details', 'kv_tour_box', 'kv_tour', 'normal', 'high' );
	add_meta_box( 'kv_tour_packages', 'Package prices (Standard, Up-Standard, Deluxe)', 'kv_packages_box', 'kv_tour', 'normal', 'high' );
	add_meta_box( 'kv_tour_departures', 'Departure dates and seats', 'kv_departures_box', 'kv_tour', 'normal', 'high' );
	add_meta_box( 'kv_tour_itinerary', 'Day-by-day itinerary', 'kv_itinerary_box', 'kv_tour', 'normal', 'default' );
	add_meta_box( 'kv_tour_extra', 'Included, excluded, travel information and FAQs', 'kv_extra_box', 'kv_tour', 'normal', 'default' );
	add_meta_box( 'kv_tour_gallery', 'Tour image gallery', 'kv_gallery_box', 'kv_tour', 'side', 'default' );
	add_meta_box( 'kv_tour_photos', 'Photos from links (optional)', 'kv_photos_box', 'kv_tour', 'normal', 'default' );
	add_meta_box( 'kv_review_details', 'Review details', 'kv_review_box', 'kv_review', 'normal', 'high' );
}

/* ---------- Repeater helpers ---------- */
function kv_repeater_row( $name, $i, $row, $fields ) {
	echo '<div class="kv-row">';
	foreach ( $fields as $key => $f ) {
		$val   = isset( $row[ $key ] ) ? $row[ $key ] : '';
		$field = $name . '[' . $i . '][' . $key . ']';
		echo '<label class="kv-f kv-f-' . esc_attr( $key ) . '"><span>' . esc_html( $f['label'] ) . '</span>';
		if ( 'textarea' === $f['type'] ) {
			echo '<textarea name="' . esc_attr( $field ) . '" rows="3">' . esc_textarea( $val ) . '</textarea>';
		} else {
			echo '<input type="' . esc_attr( $f['type'] ) . '" name="' . esc_attr( $field ) . '" value="' . esc_attr( $val ) . '"' . ( 'number' === $f['type'] ? ' min="0"' : '' ) . '>';
		}
		echo '</label>';
	}
	echo '<button type="button" class="button-link kv-remove-row">Remove</button></div>';
}

function kv_repeater( $name, $rows, $fields, $add_label ) {
	echo '<div class="kv-repeater"><div class="kv-rows">';
	foreach ( (array) $rows as $i => $row ) {
		kv_repeater_row( $name, $i, $row, $fields );
	}
	echo '</div><script type="text/template" class="kv-row-tpl">';
	kv_repeater_row( $name, '__i__', array(), $fields );
	echo '</script><p><button type="button" class="button kv-add-row">' . esc_html( $add_label ) . '</button></p></div>';
}

/* ---------- Boxes ---------- */
function kv_tour_box( $post ) {
	wp_nonce_field( 'kv_save_tour', 'kv_tour_nonce' );
	$m = function ( $k ) use ( $post ) {
		return get_post_meta( $post->ID, $k, true );
	};
	?>
	<div class="kv-grid">
		<label class="kv-f"><span>Package price per adult (<?php echo esc_html( kv_opt( 'currency' ) ); ?>)</span>
			<input type="number" min="0" step="1" name="kv_price" value="<?php echo esc_attr( $m( '_kv_price' ) ); ?>"></label>
		<label class="kv-f"><span>Price per child (leave empty to use the adult price)</span>
			<input type="number" min="0" step="1" name="kv_child_price" value="<?php echo esc_attr( $m( '_kv_child_price' ) ); ?>"></label>
		<label class="kv-f"><span>Price label (example: per person, starting price). Leave the price at 0 to show "Price on request".</span>
			<input type="text" name="kv_price_label" value="<?php echo esc_attr( $m( '_kv_price_label' ) ? $m( '_kv_price_label' ) : 'per person' ); ?>"></label>
		<label class="kv-f"><span>Duration (example: 5 Days / 4 Nights)</span>
			<input type="text" name="kv_duration" value="<?php echo esc_attr( $m( '_kv_duration' ) ); ?>"></label>
		<label class="kv-f kv-check"><input type="checkbox" name="kv_featured" value="1" <?php checked( $m( '_kv_featured' ), '1' ); ?>>
			<span>Featured / popular (shows in Popular Tour Packages on the homepage)</span></label>
	</div>
	<p class="description">Choose the destination and tour type (group, family, private, customized) in the boxes on the right. Use the main editor above for the tour description, the Excerpt box for the short card text, and Featured Image for the main photo. Publish or move to Draft to show or hide the tour.</p>
	<?php
}

function kv_packages_box( $post ) {
	$pk = kv_tour_packages( $post->ID );
	echo '<p class="description">Fill this if the tour has package prices. Solo = one person sharing a room with 4 to 5 persons. Couple = price for 2 persons in a separate room. Single = one person in a separate room. Leave all empty to use the single package price above. Booking totals use these prices.</p>';
	echo '<table class="widefat"><thead><tr><th></th>';
	foreach ( kv_room_types() as $r => $rl ) {
		echo '<th>' . esc_html( $rl ) . '</th>';
	}
	echo '</tr></thead><tbody>';
	foreach ( kv_package_tiers() as $t => $tl ) {
		echo '<tr><th>' . esc_html( $tl ) . '</th>';
		foreach ( kv_room_types() as $r => $rl ) {
			$v = isset( $pk[ $t ][ $r ] ) && $pk[ $t ][ $r ] > 0 ? $pk[ $t ][ $r ] : '';
			echo '<td><input type="number" min="0" step="1" name="kv_packages[' . esc_attr( $t ) . '][' . esc_attr( $r ) . ']" value="' . esc_attr( $v ) . '"></td>';
		}
		echo '</tr>';
	}
	echo '</tbody></table>';
}

function kv_departures_box( $post ) {
	$rows = get_post_meta( $post->ID, '_kv_departures', true );
	kv_repeater(
		'kv_departures',
		is_array( $rows ) ? $rows : array(),
		array(
			'date'  => array( 'label' => 'Departure date', 'type' => 'date' ),
			'seats' => array( 'label' => 'Total seats', 'type' => 'number' ),
		),
		'Add departure date'
	);
	echo '<p class="description">Remaining seats are calculated automatically: total seats minus travellers on bookings that are Awaiting Payment, Payment Received, Booking Confirmed or Completed. To change availability, change the total seats. Private and customized tours can be left without departure dates.</p>';
}

function kv_itinerary_box( $post ) {
	$rows = get_post_meta( $post->ID, '_kv_itinerary', true );
	kv_repeater(
		'kv_itinerary',
		is_array( $rows ) ? $rows : array(),
		array(
			'title' => array( 'label' => 'Day title (example: Day 1: Lahore to Naran)', 'type' => 'text' ),
			'text'  => array( 'label' => 'What happens on this day', 'type' => 'textarea' ),
		),
		'Add day'
	);
}

function kv_extra_box( $post ) {
	$m = function ( $k ) use ( $post ) {
		return get_post_meta( $post->ID, $k, true );
	};
	echo '<p><label class="kv-f"><span>Included services (one per line)</span><textarea name="kv_includes" rows="5">' . esc_textarea( $m( '_kv_includes' ) ) . '</textarea></label></p>';
	echo '<p><label class="kv-f"><span>Excluded services (one per line)</span><textarea name="kv_excludes" rows="5">' . esc_textarea( $m( '_kv_excludes' ) ) . '</textarea></label></p>';
	echo '<p><label class="kv-f"><span>Important travel information (one point per line)</span><textarea name="kv_info" rows="5">' . esc_textarea( $m( '_kv_info' ) ) . '</textarea></label></p>';
	echo '<h4>Tour FAQs</h4>';
	$faqs = $m( '_kv_faqs' );
	kv_repeater(
		'kv_faqs',
		is_array( $faqs ) ? $faqs : array(),
		array(
			'q' => array( 'label' => 'Question', 'type' => 'text' ),
			'a' => array( 'label' => 'Answer', 'type' => 'textarea' ),
		),
		'Add FAQ'
	);
}

function kv_gallery_box( $post ) {
	$ids = get_post_meta( $post->ID, '_kv_gallery', true );
	$ids = is_array( $ids ) ? $ids : array();
	echo '<div class="kv-gallery-wrap"><input type="hidden" class="kv-gallery-ids" name="kv_gallery" value="' . esc_attr( implode( ',', $ids ) ) . '"><div class="kv-gallery-preview">';
	foreach ( $ids as $id ) {
		echo wp_get_attachment_image( $id, 'thumbnail' );
	}
	echo '</div><p><button type="button" class="button kv-gallery-pick">Select gallery images</button> <button type="button" class="button-link kv-gallery-clear">Clear</button></p></div>';
}

function kv_photos_box( $post ) {
	$m = function ( $k ) use ( $post ) {
		return get_post_meta( $post->ID, $k, true );
	};
	echo '<p class="description">Open a photo on a free photo website (Unsplash, Pexels, Pixabay or Wikimedia Commons), right-click it, choose <strong>Copy image address</strong> and paste it here. When you click Update, the site downloads the photo into the Media Library for you.</p>';
	echo '<p><label class="kv-f"><span>Main photo link (becomes the Featured Image and the tour banner)</span><input type="url" name="kv_main_link" value="' . esc_attr( $m( '_kv_main_link' ) ) . '" placeholder="https://..."></label></p>';
	echo '<p><label class="kv-f"><span>Gallery photo links (one link per line)</span><textarea name="kv_gallery_links" rows="4" placeholder="https://...">' . esc_textarea( $m( '_kv_gallery_links' ) ) . '</textarea></label></p>';
	echo '<p><label class="kv-f"><span>Photo credit (shown on the tour page). Example: Photo by Name on Unsplash</span><input type="text" name="kv_photo_credit" value="' . esc_attr( $m( '_kv_photo_credit' ) ) . '"></label></p>';
}

function kv_review_box( $post ) {
	wp_nonce_field( 'kv_save_review', 'kv_review_nonce' );
	$rating = (int) get_post_meta( $post->ID, '_kv_rating', true );
	$rating = $rating ? $rating : 5;
	echo '<p class="description">Title = customer name. Review text goes in the main editor. Featured Image = customer photo (optional).</p>';
	echo '<p><label class="kv-f"><span>Rating</span><select name="kv_rating">';
	for ( $i = 5; $i >= 1; $i-- ) {
		echo '<option value="' . $i . '"' . selected( $rating, $i, false ) . '>' . $i . ' star' . ( 1 === $i ? '' : 's' ) . '</option>';
	}
	echo '</select></label></p>';
	echo '<p><label class="kv-f"><span>Customer city or trip (optional)</span><input type="text" name="kv_location" value="' . esc_attr( get_post_meta( $post->ID, '_kv_location', true ) ) . '"></label></p>';
}

/* ---------- Saving ---------- */
function kv_post_val( $key, $default = '' ) {
	return isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : $default; // phpcs:ignore WordPress.Security.NonceVerification
}

add_action( 'save_post_kv_tour', 'kv_save_tour' );
function kv_save_tour( $post_id ) {
	if ( ! isset( $_POST['kv_tour_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kv_tour_nonce'] ) ), 'kv_save_tour' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_kv_price', max( 0, (float) kv_post_val( 'kv_price', 0 ) ) );
	$child = kv_post_val( 'kv_child_price', '' );
	update_post_meta( $post_id, '_kv_child_price', '' === $child ? '' : max( 0, (float) $child ) );
	update_post_meta( $post_id, '_kv_duration', sanitize_text_field( kv_post_val( 'kv_duration' ) ) );
	$pk = kv_clean_packages( (array) kv_post_val( 'kv_packages', array() ) );
	if ( $pk ) {
		update_post_meta( $post_id, '_kv_packages', $pk );
	} else {
		delete_post_meta( $post_id, '_kv_packages' );
	}
	update_post_meta( $post_id, '_kv_price_label', sanitize_text_field( kv_post_val( 'kv_price_label', 'per person' ) ) );
	update_post_meta( $post_id, '_kv_featured', kv_post_val( 'kv_featured' ) ? '1' : '0' );

	$deps = array();
	foreach ( (array) kv_post_val( 'kv_departures', array() ) as $r ) {
		$date = isset( $r['date'] ) ? sanitize_text_field( $r['date'] ) : '';
		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			$deps[] = array( 'date' => $date, 'seats' => max( 0, (int) ( isset( $r['seats'] ) ? $r['seats'] : 0 ) ) );
		}
	}
	update_post_meta( $post_id, '_kv_departures', $deps );

	$days = array();
	foreach ( (array) kv_post_val( 'kv_itinerary', array() ) as $r ) {
		$title = isset( $r['title'] ) ? sanitize_text_field( $r['title'] ) : '';
		$text  = isset( $r['text'] ) ? sanitize_textarea_field( $r['text'] ) : '';
		if ( '' !== $title || '' !== $text ) {
			$days[] = array( 'title' => $title, 'text' => $text );
		}
	}
	update_post_meta( $post_id, '_kv_itinerary', $days );

	$faqs = array();
	foreach ( (array) kv_post_val( 'kv_faqs', array() ) as $r ) {
		$q = isset( $r['q'] ) ? sanitize_text_field( $r['q'] ) : '';
		$a = isset( $r['a'] ) ? sanitize_textarea_field( $r['a'] ) : '';
		if ( '' !== $q ) {
			$faqs[] = array( 'q' => $q, 'a' => $a );
		}
	}
	update_post_meta( $post_id, '_kv_faqs', $faqs );

	update_post_meta( $post_id, '_kv_includes', sanitize_textarea_field( kv_post_val( 'kv_includes' ) ) );
	update_post_meta( $post_id, '_kv_excludes', sanitize_textarea_field( kv_post_val( 'kv_excludes' ) ) );
	update_post_meta( $post_id, '_kv_info', sanitize_textarea_field( kv_post_val( 'kv_info' ) ) );

	$ids = array_filter( array_map( 'absint', explode( ',', (string) kv_post_val( 'kv_gallery' ) ) ) );
	update_post_meta( $post_id, '_kv_gallery', array_values( $ids ) );
	update_post_meta( $post_id, '_kv_photo_credit', sanitize_text_field( kv_post_val( 'kv_photo_credit' ) ) );
	kv_import_tour_photos( $post_id, kv_post_val( 'kv_main_link' ), kv_post_val( 'kv_gallery_links' ) );
}

add_action( 'save_post_kv_review', 'kv_save_review' );
function kv_save_review( $post_id ) {
	if ( ! isset( $_POST['kv_review_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kv_review_nonce'] ) ), 'kv_save_review' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, '_kv_rating', min( 5, max( 1, (int) kv_post_val( 'kv_rating', 5 ) ) ) );
	update_post_meta( $post_id, '_kv_location', sanitize_text_field( kv_post_val( 'kv_location' ) ) );
}
