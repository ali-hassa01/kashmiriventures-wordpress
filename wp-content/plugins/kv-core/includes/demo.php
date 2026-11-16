<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function kv_demo_date( $days ) {
	return wp_date( 'Y-m-d', time() + (int) $days * DAY_IN_SECONDS );
}

function kv_demo_tours() {
	$std_inc = "Hotel stay on twin sharing\nBreakfast and dinner\nPrivate air-conditioned transport\nExperienced driver and tour coordinator\nAll fuel, tolls and parking";
	$std_exc = "Lunch\nPersonal expenses\nEntry tickets and activities not mentioned\nTravel insurance\nAnything not listed under Included";
	$std_inf = "Carry a valid CNIC for hotel check-in and checkpoints\nPack warm layers, even in summer, for the evenings\nRoads and weather can change the plan; the itinerary may be adjusted for safety\nBring cash, as ATMs and cards are limited in remote areas";
	$faq     = array(
		array( 'q' => 'Where does the tour start?', 'a' => 'Pick-up points and timing are confirmed with you after booking.' ),
		array( 'q' => 'Is this tour suitable for families?', 'a' => 'Yes. Tell us the ages of children when you book so we can plan the pace and rooms.' ),
	);
	return array(
		array( 'Hunza Valley and Khunjerab Pass', 'Hunza', array( 'Group Tour', 'Family Tour' ), '5 Days / 4 Nights', 48000, 42000, 1,
			'Karimabad, Attabad Lake, Passu Cones and the Khunjerab Pass on the China border.',
			'A relaxed five-day journey into Hunza along the Karakoram Highway, with time for Baltit Fort, Attabad Lake boating and the high pass at Khunjerab.',
			array( 'Day 1: Islamabad to Chilas|Early start along the Karakoram Highway with stops at Besham and the Indus gorge. Overnight in Chilas.', 'Day 2: Chilas to Karimabad|See Nanga Parbat viewpoint and Rakaposhi viewpoint, then arrive in Karimabad for sunset at Eagle Nest.', 'Day 3: Khunjerab Pass|Drive past Passu Cones and Sost to the Khunjerab Pass at 4,693 metres. Return to Karimabad.', 'Day 4: Attabad Lake and Baltit Fort|Boat ride on Attabad Lake and a guided visit to Baltit Fort.', 'Day 5: Return|Drive back toward Islamabad.' ),
			array( 14, 30, 52 ), 14 ),
		array( 'Skardu and Deosai Plains', 'Skardu', array( 'Group Tour' ), '6 Days / 5 Nights', 56000, 50000, 1,
			'Shangrila Lake, Shigar Fort, Katpana Cold Desert and the roof of the world at Deosai.',
			'Six days around Skardu: turquoise lakes, a restored fort and the high plateau of Deosai in the open summer season.',
			array( 'Day 1: Arrival in Skardu|Arrive and rest. Evening walk at Skardu bazaar.', 'Day 2: Shangrila and Upper Kachura|Visit Shangrila Resort and the Kachura lakes.', 'Day 3: Deosai Plains|Full day on the Deosai plateau, Sheosar Lake and wild flowers.', 'Day 4: Shigar and Khaplu|Shigar Fort and the Khaplu Palace.', 'Day 5: Katpana Cold Desert|Sand dunes at altitude and Manthal Buddha Rock.', 'Day 6: Departure|Transfer to the airport or start the road journey back.' ),
			array( 10, 25 ), 12 ),
		array( 'Naran Kaghan and Saif-ul-Malook', 'Naran Kaghan', array( 'Group Tour', 'Family Tour' ), '4 Days / 3 Nights', 29000, 25000, 1,
			'Shogran, Lake Saif-ul-Malook by jeep, Lulusar Lake and Babusar Top.',
			'A relaxed Kaghan Valley circuit for families and friends, with jeep rides to Saif-ul-Malook and the Babusar Top viewpoint.',
			array( 'Day 1: Journey to Naran|Drive to Naran via Balakot with a riverside lunch stop.', 'Day 2: Saif-ul-Malook|Jeep ride to the lake and free time for walks and photos.', 'Day 3: Babusar Top and Lulusar|Scenic drive up to Babusar Top, stop at Lulusar Lake.', 'Day 4: Return|Breakfast and drive back.' ),
			array( 8, 22, 36 ), 16 ),
		array( 'Swat, Kalam and Malam Jabba', 'Swat Kalam', array( 'Group Tour', 'Family Tour' ), '3 Days / 2 Nights', 24000, 21000, 0,
			'Malam Jabba chairlift, Kalam Valley and Mahodand Lake.',
			'A short and easy getaway to the green valleys of Swat, ideal for a long weekend.',
			array( 'Day 1: Malam Jabba|Drive to Swat, chairlift and Green Top viewpoint.', 'Day 2: Kalam and Mahodand|Jeep to Mahodand Lake and Ushu Forest.', 'Day 3: Return|Breakfast and drive back.' ),
			array( 7, 21 ), 14 ),
		array( 'Neelum Valley and Arang Kel', 'Kashmir', array( 'Group Tour' ), '4 Days / 3 Nights', 32000, 28000, 0,
			'Keran, Sharda, Kutton Waterfall and the cable-car and hike to Arang Kel.',
			'The green heart of Azad Kashmir: river views, wooden villages and the walk up to Arang Kel.',
			array( 'Day 1: Journey to Muzaffarabad and Keran|Scenic drive along the Neelum River.', 'Day 2: Sharda and Kutton|Visit Sharda ruins and Kutton Waterfall.', 'Day 3: Arang Kel|Cable car and hike to Arang Kel meadows.', 'Day 4: Return|Drive back.' ),
			array( 12, 33 ), 12 ),
		array( 'Fairy Meadows and Nanga Parbat Base', 'Fairy Meadows', array( 'Group Tour' ), '3 Days / 2 Nights', 38000, 38000, 1,
			'Jeep to Tato village, trek to Fairy Meadows and a view of Nanga Parbat.',
			'A two-night mountain stay under Nanga Parbat. Moderate fitness needed for the trek.',
			array( 'Day 1: Raikot Bridge to Fairy Meadows|Jeep to Tato, then a guided 3 to 4 hour trek.', 'Day 2: Beyal Camp|Walk to Beyal Camp for the best views of Nanga Parbat.', 'Day 3: Descent|Trek down and drive back.' ),
			array( 15, 40 ), 10 ),
		array( 'Private Northern Areas Tour (Customized)', 'Hunza', array( 'Private Tour', 'Customized Tour' ), 'Your dates, your pace', 70000, 70000, 0,
			'A private vehicle, your own route and hotels at the level you choose.',
			'Tell us the destinations, number of days, group size and budget. We design the itinerary and send you a quote.',
			array( 'Day 1: Plan together|Share your ideas with us on WhatsApp or the booking form. We build a day-by-day route and agree the price before you pay anything.' ),
			array(), 0 ),
	);
}

function kv_load_demo_content() {
	$std = array(
		'inc' => "Hotel stay on twin sharing\nBreakfast and dinner\nPrivate air-conditioned transport\nExperienced driver and tour coordinator\nAll fuel, tolls and parking",
		'exc' => "Lunch\nPersonal expenses\nEntry tickets and activities not mentioned\nTravel insurance\nAnything not listed under Included",
		'inf' => "Carry a valid CNIC for hotel check-in and checkpoints\nPack warm layers, even in summer, for the evenings\nRoads and weather can change the plan; the itinerary may be adjusted for safety\nBring cash, as ATMs and cards are limited in remote areas",
	);
	foreach ( kv_demo_tours() as $d ) {
		list( $title, $dest, $types, $dur, $price, $child, $feat, $excerpt, $content, $days, $offsets, $seats ) = $d;
		$exists = get_posts( array( 'post_type' => 'kv_tour', 'title' => $title, 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) );
		if ( $exists ) {
			continue;
		}
		$id = wp_insert_post( array( 'post_type' => 'kv_tour', 'post_status' => 'publish', 'post_title' => $title, 'post_excerpt' => $excerpt, 'post_content' => $content ) );
		if ( is_wp_error( $id ) || ! $id ) {
			continue;
		}
		$term = term_exists( $dest, 'kv_destination' );
		if ( ! $term ) {
			$term = wp_insert_term( $dest, 'kv_destination' );
		}
		if ( ! is_wp_error( $term ) ) {
			wp_set_object_terms( $id, (int) ( is_array( $term ) ? $term['term_id'] : $term ), 'kv_destination' );
		}
		wp_set_object_terms( $id, $types, 'kv_tour_type' );
		$itin = array();
		foreach ( $days as $day ) {
			$p      = explode( '|', $day, 2 );
			$itin[] = array( 'title' => $p[0], 'text' => isset( $p[1] ) ? $p[1] : '' );
		}
		$deps = array();
		foreach ( $offsets as $o ) {
			$deps[] = array( 'date' => kv_demo_date( $o ), 'seats' => $seats );
		}
		update_post_meta( $id, '_kv_price', $price );
		update_post_meta( $id, '_kv_child_price', $child );
		update_post_meta( $id, '_kv_duration', $dur );
		update_post_meta( $id, '_kv_featured', $feat ? '1' : '0' );
		update_post_meta( $id, '_kv_departures', $deps );
		update_post_meta( $id, '_kv_itinerary', $itin );
		update_post_meta( $id, '_kv_includes', $std['inc'] );
		update_post_meta( $id, '_kv_excludes', $std['exc'] );
		update_post_meta( $id, '_kv_info', $std['inf'] );
		update_post_meta( $id, '_kv_faqs', array(
			array( 'q' => 'Where does the tour start?', 'a' => 'Pick-up points and timing are confirmed with you after booking.' ),
			array( 'q' => 'Is this tour suitable for families?', 'a' => 'Yes. Tell us the ages of children when you book so we can plan the pace and rooms.' ),
		) );
		update_post_meta( $id, '_kv_demo', '1' );
	}

	$reviews = array(
		array( 'Ayesha K.', 5, 'Hunza trip', 'Everything was organised, from the first message to the last hotel. The driver knew every viewpoint.' ),
		array( 'Hamza R.', 5, 'Skardu and Deosai', 'Clear plan, fair price and no surprises. Deosai at sunrise was unforgettable.' ),
		array( 'Sana M.', 4, 'Naran family trip', 'Our kids loved the jeep ride to Saif-ul-Malook. The team answered every question on WhatsApp.' ),
		array( 'Bilal A.', 5, 'Swat long weekend', 'Short trip, perfect pace. Hotels were clean and the schedule was relaxed.' ),
		array( 'Mariam S.', 5, 'Neelum Valley', 'Beautiful route and a friendly coordinator. We would travel with them again.' ),
	);
	foreach ( $reviews as $r ) {
		$id = wp_insert_post( array( 'post_type' => 'kv_review', 'post_status' => 'publish', 'post_title' => $r[0], 'post_content' => $r[3] ) );
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_kv_rating', $r[1] );
			update_post_meta( $id, '_kv_location', $r[2] );
			update_post_meta( $id, '_kv_demo', '1' );
		}
	}

	foreach ( array( 'Hunza Valley', 'Attabad Lake', 'Shangrila Lake', 'Deosai Plains', 'Lake Saif-ul-Malook', 'Mahodand Lake', 'Arang Kel', 'Fairy Meadows' ) as $g ) {
		$id = wp_insert_post( array( 'post_type' => 'kv_gallery', 'post_status' => 'publish', 'post_title' => $g ) );
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_kv_demo', '1' );
		}
	}

	$faqs = array(
		array( 'How do I book a tour?', 'Choose a tour, pick a departure date and submit the booking form. Our team contacts you to confirm details and the advance payment.' ),
		array( 'Is my booking confirmed right after I submit the form?', 'No. The form sends a booking request. Your seats are confirmed once our team contacts you and the agreed advance is received.' ),
		array( 'Can I customize a tour?', 'Yes. Choose a Private or Customized tour, or send us your idea on WhatsApp.' ),
		array( 'What is the best season to travel to the northern areas?', 'Most routes are best from May to October. Some tours, such as cherry blossom trips, run in spring.' ),
		array( 'How can I pay?', 'We accept bank transfer, JazzCash, Easypaisa and cash at the office. Payment details are shared by our team after you book.' ),
	);
	foreach ( $faqs as $i => $f ) {
		$id = wp_insert_post( array( 'post_type' => 'kv_faq', 'post_status' => 'publish', 'post_title' => $f[0], 'post_content' => $f[1], 'menu_order' => $i ) );
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_kv_demo', '1' );
		}
	}
}

add_action( 'admin_post_kv_load_demo', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You are not allowed to do this.' );
	}
	check_admin_referer( 'kv_demo' );
	kv_load_demo_content();
	kv_flash( 'success', 'Demo content added. Open the website to see it. Remove it later with the Remove demo content button.' );
	wp_safe_redirect( admin_url( 'admin.php?page=kv-settings' ) );
	exit;
} );

add_action( 'admin_post_kv_remove_demo', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You are not allowed to do this.' );
	}
	check_admin_referer( 'kv_demo' );
	$ids = get_posts( array( 'post_type' => array( 'kv_tour', 'kv_review', 'kv_gallery', 'kv_faq' ), 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_kv_demo', 'meta_value' => '1' ) );
	foreach ( $ids as $id ) {
		wp_delete_post( $id, true );
	}
	kv_flash( 'success', 'Demo tours, reviews, gallery items and FAQs removed. Bookings were not touched.' );
	wp_safe_redirect( admin_url( 'admin.php?page=kv-settings' ) );
	exit;
} );
