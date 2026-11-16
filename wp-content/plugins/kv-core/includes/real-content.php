<?php
/**
 * Real Kashmiri Ventures content taken from the client's WhatsApp catalogue:
 * tours and itineraries, FAQs and the policy pages.
 * No package prices were supplied, so tours show "Price on request" until a price is entered in wp-admin.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function kv_days( $days ) {
	$out = array();
	foreach ( $days as $title => $items ) {
		$out[] = array( 'title' => $title, 'text' => '• ' . implode( "\n• ", $items ) );
	}
	return $out;
}

function kv_real_tours() {
	$dep   = 'Group trips leave from Lahore / Faisalabad at 10:00 PM. Members from Islamabad are picked up on Day 1.';
	$info  = $dep . "\nOriginal CNIC is mandatory for every traveller.\nSeats are allotted before departure and shuffled during the trip. A specific seat costs Rs. 1,000 per seat per day extra.\nThe itinerary can change because of weather, road closures or local events.\nPlease read the Terms and Conditions and the Cancellation Policy before booking.";
	$back  = array( 'Wake-up call', 'Breakfast', 'Travel back', 'Short stays on the way', 'Back to Islamabad', 'Back to Lahore / Faisalabad' );
	$types = array( 'Group Tour', 'Family Tour' );

	return array(
		array(
			'title' => '4 Days Neelum Valley, Taobat and Arang Kel', 'dest' => array( 'Kashmir' ), 'types' => $types, 'duration' => '4 Days', 'featured' => 1,
			'excerpt' => 'Dhani and Kutton waterfalls, the LOC view, Keran, Sharda, Taobat and the hike to Arang Kel.',
			'content' => 'A four-day trip through Neelum Valley in Azad Kashmir: waterfalls, the Line of Control viewpoint, Keran and Sharda, a jeep ride to Taobat and the hike to Arang Kel. The group leaves from Lahore / Faisalabad at 10:00 PM and members are picked up from Islamabad.',
			'days' => array(
				'Day 0: Departure' => array( 'Departure from Lahore / Faisalabad at 10:00 PM' ),
				'Day 1: Islamabad to Keran / Sharda' => array( 'Reach Islamabad, pick up members', 'Departure for Neelum Valley', 'Breakfast at Muzaffarabad', 'Reach Dhani Waterfall', 'Explore Kutton Waterfall', 'Explore LOC', 'Reach Keran Valley / Sharda Valley', 'Dinner', 'Overnight stay' ),
				'Day 2: Taobat' => array( 'Wake-up call', 'Breakfast at the same place', 'Travel towards Kel', 'Transfer into jeeps for Taobat', 'Visit Jamgarh Waterfall', 'Visit Taobat', 'Visit Halmat', 'Back to hotel', 'Dinner', 'Overnight stay and bonfire' ),
				'Day 3: Arang Kel' => array( 'Breakfast in hotel at 8:00 AM', 'Transfer to jeeps for Kel', 'Departure for Kel at 9:00 AM', 'Explore Kel', 'Hike to Arang Kel', 'Visit Arang Kel', 'Drive back to Kel', 'Dinner and night stay in Keran / Sharda' ),
				'Day 4: Return' => $back,
			),
		),
		array(
			'title' => '5 Days Fairy Meadows and Naran Valley', 'dest' => array( 'Fairy Meadows', 'Naran Kaghan' ), 'types' => array( 'Group Tour' ), 'duration' => '5 Days', 'featured' => 1,
			'excerpt' => 'Jeep safari to Tattu Village, trek to Fairy Meadows, Bayal Camp and a stay in Naran.',
			'content' => 'A five-day trip with a night at Fairy Meadows under Nanga Parbat. Includes a jeep safari to Tattu Village, a 4 hour trek to Fairy Meadows, the trek to Bayal Camp, and stops at Lulusar Lake and Babusar Top.',
			'days' => array(
				'Day 0: Departure' => array( 'Departure from Lahore / Faisalabad at 10:00 PM' ),
				'Day 1: Islamabad to Chilas' => array( 'Reach Islamabad, pick up members', 'Departure for Chilas', 'Breakfast at Balakot', 'Reach Naran Valley', 'Short stay at Lulusar Lake', 'Short stay at Babusar Top', 'Reach Chilas and dinner', 'Overnight stay' ),
				'Day 2: Fairy Meadows' => array( 'Breakfast in hotel at 8:00 AM', 'Travel towards Raikot Bridge', 'Jeep safari to Tattu Village (2 hours)', 'Trek to Fairy Meadows (4 hours)', 'Reach Fairy Meadows', 'Dinner and night stay' ),
				'Day 3: Bayal Camp' => array( 'Breakfast at hotel', 'Start trek towards Bayal Camp (2.5 hours)', 'Back to Fairy Meadows (3 to 4 hours)', 'Back to hotel', 'Night stay and dinner' ),
				'Day 4: Down to Naran' => array( 'Early breakfast', 'Trek back to Tattu Village', 'Transfer into jeeps to Raikot Bridge', 'Start travelling towards Naran', 'Night stay and dinner in Naran' ),
				'Day 5: Return' => $back,
			),
		),
		array(
			'title' => '4 Days Kumrat Valley, Katora Lake and Jahaz Bhanda', 'dest' => array( 'Kumrat Valley' ), 'types' => $types, 'duration' => '4 Days', 'featured' => 0,
			'excerpt' => 'Kala Chashma and Kumrat waterfall, jeep to Takki Top, Jahaz Bhanda and the hike to Katora Lake.',
			'content' => 'A four-day trip to Kumrat Valley in Upper Dir with a jeep ride to Takki Top, a night at Jahaz Bhanda and a 2 hour hike to Katora Lake.',
			'days' => array(
				'Day 0: Departure' => array( 'Departure from Lahore / Faisalabad at 10:00 PM' ),
				'Day 1: Kumrat Valley' => array( 'Reach Islamabad, pick up members', 'Departure for Kumrat Valley and breakfast', 'Reach Thal Bazar', 'Transfer into jeeps', 'Explore Kala Chashma and Kumrat Waterfall', 'Explore Kumrat', 'Dinner and night stay at Kumrat Forest' ),
				'Day 2: Jahaz Bhanda' => array( 'Wake-up call', 'Breakfast at the same place', 'Transfer into jeeps', 'Reach Takki Top', 'Hike to Jahaz Bhanda', 'Reach Jahaz Bhanda', 'Check in to hotel', 'Dinner', 'Overnight stay' ),
				'Day 3: Katora Lake' => array( 'Breakfast', 'Hike to Katora Lake (2 hours)', 'Explore Katora Lake', 'Back to Jahaz Bhanda', 'Hike to Kundh Bhanda and explore', 'Back to hotel', 'Overnight stay, dinner and bonfire' ),
				'Day 4: Return' => $back,
			),
		),
		array(
			'title' => '5 Days Hunza Valley and Naran Valley', 'dest' => array( 'Hunza', 'Naran Kaghan' ), 'types' => $types, 'duration' => '5 Days', 'featured' => 1,
			'excerpt' => 'Rakaposhi viewpoint, Altit and Baltit forts, Attabad Lake, Passu Cones and Khunjerab National Park.',
			'content' => 'A five-day trip to Hunza along the Karakoram Highway with Babusar Top and Lulusar Lake on the way, forts and bazaar in Karimabad, Attabad Lake, Passu, Gojal and Khunjerab National Park, then a jeep trip to Naltar Valley.',
			'days' => array(
				'Day 0: Departure' => array( 'Departure from Lahore / Faisalabad at 10:00 PM' ),
				'Day 1: Islamabad to Chilas' => array( 'Reach Islamabad, pick up members', 'Departure for Chilas / Naran', 'Reach Chilas and dinner', 'Overnight stay' ),
				'Day 2: Hunza Valley' => array( 'Breakfast in hotel at 8:00 AM', 'Babusar Top and Lulusar Lake', 'Travel towards Hunza Valley', 'Visit Mountain Ranges Junction', 'Visit Rakaposhi View Point', 'Visit Altit Fort, Baltit Fort and Karimabad Bazaar', 'Dinner and night stay' ),
				'Day 3: Upper Hunza' => array( 'Breakfast at hotel', 'Travel towards Attabad Tunnel and Attabad Lake', 'Hike to Hussaini Suspension Bridge (5 minutes)', 'Visit Passu Glacier, Passu Cones and Gojal', 'Visit China Border and Khunjerab National Park', 'Back to hotel', 'Night stay and dinner' ),
				'Day 4: Naltar Valley' => array( 'Early breakfast', 'Transfer into jeeps for Naltar Valley', 'Explore Naltar Valley', 'Start travelling', 'Night stay and dinner at Chilas / Naran' ),
				'Day 5: Return' => array( 'Wake-up call', 'Breakfast', 'Travel back', 'Short stays on the way', 'Back to Islamabad', 'Back to Lahore' ),
			),
		),
		array(
			'title' => '5 Days Neelum Valley with Ratti Gali Lake', 'dest' => array( 'Kashmir' ), 'types' => $types, 'duration' => '5 Days', 'featured' => 0,
			'excerpt' => 'Ratti Gali Lake, Sharda, Arang Kel with a doli ride, Taobat and a bonfire night.',
			'content' => 'A five-day Neelum Valley trip with a hike to Ratti Gali Lake from the base camp, Sharda sightseeing, the Arang Kel doli ride and hike, and a bonfire with music at Taobat.',
			'days' => array(
				'Day 0: Departure' => array( 'Departure from Lahore' ),
				'Day 1: Keran' => array( 'Pick up members from Islamabad (if any) at 26# Daewoo Terminal', 'Breakfast at Muzaffarabad (Panjgran)', 'Visit Dhani Waterfall', 'Visit LOC point', 'Visit Kutton Waterfall', 'Dinner and night stay at Keran' ),
				'Day 2: Ratti Gali Lake' => array( 'Breakfast', 'Travel to Doarian', 'Shift into jeeps', 'Move to Ratti Gali base camp', '45 minute hike from base camp to the lake', 'Explore the lake', 'Back to Doarian', 'Shift to coaster and move to Sharda', 'Dinner and night stay at Sharda' ),
				'Day 3: Arang Kel and Taobat' => array( 'Breakfast', 'Sightseeing in Sharda', 'Shift into jeeps and travel to Kel', 'Enjoy the doli ride', '30 minute steep hike', 'Visit Arang Kel', 'Shift into jeeps and move to Taobat', 'Dinner at Taobat', 'Night stay in Taobat', 'Bonfire and music' ),
				'Day 4: Taobat and Sharda' => array( 'Breakfast', 'Visit Taobat', 'Visit the bridge', 'Back to Sharda', 'Dinner and night stay at Sharda' ),
				'Day 5: Return' => array( 'Breakfast', 'Travel back to Lahore', 'Short stay at Panjgran Waterfall', 'Short stay at Barah Koh', 'Dinner on the way (decided by the tour guide)', 'Arrival in Lahore' ),
			),
		),
		array(
			'title' => '6 Days Skardu Valley', 'dest' => array( 'Skardu' ), 'types' => array( 'Group Tour' ), 'duration' => '6 Days', 'featured' => 1,
			'excerpt' => 'Shangrila Lake, Upper Kachura, Shigar Fort, Cold Desert, Manthoka Waterfall and Basho Valley.',
			'content' => 'A six-day trip to Skardu: Nanga Parbat viewpoint, Shangrila and Upper Kachura lakes, Shigar Valley and fort, the cold desert, Manthoka Waterfall and a 4x4 jeep trip to Basho Valley and Sultanabad Meadows.',
			'days' => array(
				'Day 0: Departure' => array( 'Departure from Lahore / Faisalabad at 10:00 PM' ),
				'Day 1: Islamabad to Chilas' => array( 'Reach Islamabad, pick up members', 'Departure for Chilas', 'Reach Chilas / Naran and dinner', 'Overnight stay' ),
				'Day 2: Skardu' => array( 'Breakfast in hotel at 8:00 AM', 'Short stay at Nanga Parbat View Point and Mountain Junction Point', 'Short stay at Astak Nala', 'Visit Shangrila Lake', 'Visit Upper Kachura Lake', 'Reach Skardu Valley', 'Dinner and night stay' ),
				'Day 3: Shigar Valley' => array( 'Breakfast at hotel', 'Departure for Shigar Valley', 'Visit Shigar Cold Desert', 'Visit Shigar Valley and Shigar Fort', 'Visit Manthoka Waterfall, Kharmang', 'Back to Skardu Valley', 'Back to hotel', 'Night stay and dinner' ),
				'Day 4: Basho Valley' => array( 'Early breakfast', 'Departure for Basho Valley', 'Transfer into 4x4 jeeps', 'Visit Basho Valley', 'Visit Sultanabad Meadows', 'Back to Skardu', 'Night stay and dinner' ),
				'Day 5: Back towards Chilas' => array( 'Breakfast', 'Short stay at Astak Nala', 'Reach Chilas / Naran', 'Night stay and dinner' ),
				'Day 6: Return' => $back,
			),
		),
		array(
			'title' => '6 Days Astore Valley and Minimarg', 'dest' => array( 'Astore Minimarg' ), 'types' => array( 'Group Tour' ), 'duration' => '6 Days', 'featured' => 0,
			'excerpt' => 'Rama Meadows and Rama Lake, camping, Minimarg via Burzil Pass and Domel Valley.',
			'content' => 'A six-day adventure to Astore: camping at Rama Meadows, Rama Lake, the scenic drive over Burzil Pass to Minimarg, Domel Valley, Chilim and Sheosar Lake in Deosai.',
			'days' => array(
				'Day 0: Departure' => array( 'Departure from Lahore at 10:00 PM' ),
				'Day 1: To Chilas' => array( 'Departure from Islamabad at 5:30 AM', 'Breakfast at Balakot', 'Reach Naran', 'Short stay at Lulusar Lake', 'Continue towards Babusar Pass', 'Short stay at Babusar Top', 'Reach Chilas', 'Dinner', 'Night stay in Chilas / Guner Farm' ),
				'Day 2: Rama Meadows' => array( 'Breakfast at hotel', 'Depart from hotel at 8:30 AM', 'Reach Astore', 'Transfer to jeeps and drive towards Rama Meadows through scenic valleys', 'Visit Rama Lake and spend time exploring', 'Dinner and bonfire', 'Night stay and camping at Rama Meadows' ),
				'Day 3: Minimarg' => array( 'Breakfast at hotel', 'Depart for Minimarg Valley', 'Reach Chilim Valley', 'Entries of all participants', 'Move towards Minimarg', 'Scenic ride via Burzil Pass', 'Reach Minimarg', 'Photography and refreshment break', 'Reach Domel Valley', 'Dinner and night stay', 'Camping or huts at Domel / Chilim Valley' ),
				'Day 4: Sheosar, Deosai' => array( 'Breakfast at hotel', 'Departure for Sheosar, Deosai', 'Back to Minimarg and short break', 'Reach Chilim Valley', 'Drive towards Sheosar and rest there', 'Back to Astore in the evening', 'Dinner and night stay at Chilim or Astore city' ),
				'Day 5: Towards Naran' => array( 'Depart for Naran via Babusar, through scenic valleys', 'Short stay at Babusar Top', 'Lunch stop at Moon Restaurant in Besal', 'Continue towards Naran', 'Night stay in Baruwal or Naran' ),
				'Day 6: Return' => array( 'Breakfast at hotel', 'Departure for Islamabad and Lahore at 10:00 AM', 'Short stays on the way', 'Reach Islamabad in the evening', 'Reach Lahore late at night' ),
			),
		),
		array(
			'title' => '7 Days Kashmir, Shogran, Swat and Kalam', 'dest' => array( 'Kashmir', 'Shogran', 'Swat Kalam' ), 'types' => $types, 'duration' => '7 Days / 6 Nights', 'featured' => 0,
			'excerpt' => 'Neelum Valley, Arang Kel, Shogran and Siri Paye Meadows, then Swat, Kalam, Mahodand Lake and Malam Jabba.',
			'content' => 'A seven-day special tour (7 days and 6 nights) from Islamabad covering Murree Expressway, Neelum Valley (Dhani and Kutton waterfalls, Neelum Jhelum Dam, Keran, LOC, Sharda, Kel, Arang Kel), Balakot, Shogran and Siri Paye Meadows, then Swat (Fizzaghat, Malam Jabba chairlift and zipline), Kalam, Ushu Forest, Palogha Village and Mahodand Lake.',
			'days' => array(
				'Day 1: Neelum Valley' => array( 'Breakfast at hotel', 'Departure for Neelum Valley', 'Reach Muzaffarabad', 'Short stay at Dhani Waterfall', 'Short stay at Neelum Jhelum Dam', 'Reach Keran and check in', 'Dinner and night stay in Sharda / Keran' ),
				'Day 2: Arang Kel' => array( 'Breakfast at hotel', 'Shift into jeeps', 'Travel towards Kel', 'Reach Kel', 'Hike to Arang Kel (30 to 35 minutes)', 'Explore Arang Kel', 'Back to Sharda / Keran', 'Night stay in Sharda / Keran' ),
				'Day 3: Shogran' => array( 'Breakfast at hotel', 'Travel from Keran to Balakot', 'Visit LOC', 'Visit Kutton Waterfall', 'Reach Muzaffarabad', 'Move towards Balakot', 'Reach Kiwai and short stay at Kiwai Waterfall', 'Shift into jeeps', 'Reach Shogran and explore', 'Travel to Siri Paye Meadows and spend time there', 'Back to Balakot', 'Dinner and night stay at Balakot' ),
				'Day 4: Swat and Kalam' => array( 'Breakfast at hotel', 'Departure for Swat and Kalam', 'Short stay at Fizzaghat', 'Reach Kalam', 'Visit Mall Road Kalam', 'Dinner and night stay at Kalam' ),
				'Day 5: Mahodand Lake' => array( 'Breakfast at hotel', 'Shift into jeeps', 'Departure for Mahodand Lake', 'Visit Ushu Forest', 'Visit Palogha Village', 'Dinner and night stay at Kalam' ),
				'Day 6: Malam Jabba' => array( 'Breakfast at hotel', 'Travel towards Malam Jabba', 'Visit PC Hotel Malam Jabba', 'Zipline and chairlift (charges not included)', 'Dinner and night stay at Malam Jabba' ),
				'Day 7: Return' => array( 'Breakfast, then departure for Islamabad', 'Move towards Islamabad', 'Drop at Islamabad' ),
			),
		),
		array(
			'title' => '8 Days Grand Trip to Skardu and Hunza', 'dest' => array( 'Skardu', 'Hunza' ), 'types' => array( 'Group Tour' ), 'duration' => '8 Days', 'featured' => 0,
			'excerpt' => 'Skardu, Deosai National Park, Sarfaranga Cold Desert, Hunza, Passu Cones and Khunjerab Pass.',
			'content' => 'The grand northern trip: Skardu with Shangrila and Upper Kachura lakes, Sarfaranga Cold Desert and Manthoka Waterfall, Deosai National Park (if open) with Sadpara Lake, Bara Pani, Kala Pani and Sheosar Lake, then Hunza with Baltit Fort, Attabad Lake, Hussaini Bridge, Passu Cones, Sost border and Khunjerab Pass (if accessible).',
			'days' => array(
				'Day 1: To Chilas' => array( 'Reach Islamabad, pick up members', 'Departure for Chilas', 'Breakfast at Balakot', 'Reach Chilas / Naran and dinner', 'Overnight stay' ),
				'Day 2: Skardu' => array( 'Breakfast in hotel at 8:00 AM', 'Short stay at Nanga Parbat View Point and Mountain Junction Point', 'Short stay at Astak Nala', 'Visit Shangrila Lake', 'Visit Upper Kachura Lake', 'Reach Skardu Valley', 'Dinner and night stay' ),
				'Day 3: Sarfaranga' => array( 'Breakfast at hotel', 'Visit Sarfaranga Cold Desert', 'Visit Manthoka Waterfall, Kharmang', 'Back to Skardu Valley', 'Back to hotel', 'Night stay and dinner' ),
				'Day 4: Deosai' => array( 'Breakfast', 'If Deosai remains open, visit Deosai National Park', 'Visit Sadpara Lake', 'Visit Bara Pani', 'Visit Kala Pani', 'Visit Sheosar Lake', 'Back to Skardu', 'Dinner and overnight stay' ),
				'Day 5: Hunza' => array( 'Early breakfast', 'Move towards Hunza Valley', 'Reach Hunza', 'Visit Altit Fort (optional)', 'Short hike to Baltit Fort (optional)', 'Visit historic Baltit Fort', 'Visit Karimabad Bazaar', 'Night stay and dinner', 'Note: if you visit Naltar Valley, Altit and Baltit forts and Karimabad Bazaar can be skipped' ),
				'Day 6: Khunjerab' => array( 'Early breakfast', 'Move to Khunjerab Pass (Pak-China border)', 'Visit Attabad Lake', 'Visit Hussaini Bridge', 'Visit Passu Cones', 'Short stay at Sost border', 'Visit Khunjerab Pass (if accessible)', 'Back to Hunza', 'Dinner and overnight stay' ),
				'Day 7: Towards Chilas' => array( 'Breakfast', 'Short stay at Astak Nala', 'Reach Chilas / Besham / Naran', 'Night stay and dinner' ),
				'Day 8: Return' => array( 'Wake-up call', 'Breakfast', 'Lulusar Lake', 'Babusar Top', 'Short stays on the way', 'Back to Islamabad' ),
			),
			'extra_info' => "Seats are allotted before departure by the tour management for your convenience and are shuffled during the trip. A jumper is also included in the trip.\nFor any specific seat demand, Rs. 1,000 is charged per seat per day.",
		),
		array(
			'title' => '3 Days Swat, Kalam and Malam Jabba', 'dest' => array( 'Swat Kalam' ), 'types' => $types, 'duration' => '3 Days', 'featured' => 1,
			'excerpt' => 'Malam Jabba, Kalam Valley, Ushu Forest, Palogha Valley and Mahodand Lake.',
			'content' => 'A three-day getaway to Swat: Malam Jabba, Kalam Valley, a jeep trip to Ushu Forest, Palogha Valley and Mahodand Lake (if accessible), with a bonfire night.',
			'days' => array(
				'Day 0: Departure' => array( 'Departure from Lahore / Faisalabad at 10:00 PM' ),
				'Day 1: Swat to Kalam' => array( 'Reach Islamabad, pick up members', 'Departure for Swat Valley', 'Breakfast at Fizzaghat', 'Reach and explore Malam Jabba', 'Reach Kalam Valley', 'Dinner', 'Overnight stay' ),
				'Day 2: Ushu Forest' => array( 'Wake-up call', 'Breakfast at the same place', 'Travel to Ushu Forest in jeeps', 'Short stay at Ushu Forest', 'Explore Palogha Valley', 'Explore Mahodand Lake (if accessible)', 'Back to hotel', 'Dinner', 'Overnight stay and bonfire' ),
				'Day 3: Return' => array( 'Wake-up call', 'Breakfast', 'Travel back', 'Short stays on the way', 'Back to Islamabad', 'Back to Lahore / Faisalabad', 'End of tour' ),
			),
		),
		array(
			'title' => '3 Days Kashmir and Ratti Gali Lake', 'dest' => array( 'Kashmir' ), 'types' => $types, 'duration' => '3 Days', 'featured' => 0,
			'excerpt' => 'Dhani and Kutton waterfalls, LOC, Keran and a jeep ride and hike to Ratti Gali Lake.',
			'content' => 'A short three-day Neelum Valley trip with a jeep ride to the Ratti Gali base camp, a hike to the lake and a bonfire night.',
			'days' => array(
				'Day 0: Departure' => array( 'Departure from Lahore / Faisalabad at 10:00 PM' ),
				'Day 1: Keran' => array( 'Reach Islamabad, pick up members', 'Departure for Neelum Valley', 'Breakfast at Muzaffarabad', 'Reach Dhani Waterfall', 'Explore Kutton Waterfall', 'Explore LOC', 'Reach Keran Valley', 'Dinner', 'Overnight stay' ),
				'Day 2: Ratti Gali Lake' => array( 'Wake-up call', 'Breakfast at the same place', 'Travel towards Ratti Gali Lake', 'Transfer into jeeps', 'Reach Ratti Gali base camp', 'Hike to the lake (15 to 20 minutes)', 'Explore Ratti Gali Lake', 'Back to hotel', 'Dinner', 'Overnight stay and bonfire' ),
				'Day 3: Return' => $back,
			),
		),
		array(
			'title' => '3 Days Naran, Kaghan and Babusar Pass', 'dest' => array( 'Naran Kaghan' ), 'types' => array( 'Group Tour', 'Family Tour' ), 'duration' => '3 Days / 2 Nights', 'featured' => 1,
			'excerpt' => 'Naran Bazaar, Battakundi, Jalkhad, Lulusar Lake and Babusar Pass with a bonfire and BBQ night.',
			'content' => 'A three-day tour to Naran, Kaghan and Babusar Pass. Departure: every Thursday and Monday night from Faisalabad, Lahore and Islamabad, at 10:00 PM sharp. Rooms are 4 to 5 person sharing for solo travellers or a 2 person room for couples.',
			'days' => array(
				'Day 0 (Thursday): Departure' => array( 'Departure at 10:00 PM sharp' ),
				'Day 1 (Friday): Naran' => array( 'Short stop at Bhera', 'Travel via Hazara, Mansehra and Balakot', 'Breakfast at Balakot or Kiwai', 'Explore Naran Bazaar', 'Dinner and overnight stay in Naran' ),
				'Day 2 (Saturday): Babusar Pass' => array( 'Breakfast', 'Visit Battakundi, Jalkhad, Lulusar Lake and Babusar Pass (subject to road conditions)', 'Optional: visit Lake Saif-ul-Malook (jeep charges not included)', 'Evening bonfire and BBQ night', 'Overnight stay in Naran' ),
				'Day 3 (Sunday): Return' => array( 'Breakfast', 'Travel back via Kiwai Waterfall', 'Scenic stops and photography', 'Return to Faisalabad by night' ),
			),
			'includes' => "A/C transport (Coaster / Hiace)\nFuel, toll taxes and road expenses\n2-night hotel accommodation (beds and mattresses)\n3 breakfasts and 2 dinners (standard menu)\nBBQ and music night\nOn-tour professional guide\nGroup photography\nBasic first aid",
			'excludes' => "Jeep ride to Lake Saif-ul-Malook\nBoating / river rafting\nMineral water, cold drinks and personal snacks\nExtra expenses due to unforeseen events\nAnything not mentioned under Included",
			'extra_info' => "Registration: advance payment of Rs. 3,000 per person; the remaining amount is paid at the time of departure.\nChildren: under 3 years travel free (on lap only); 8 years and above pay full charges.\nRecommended to bring: original CNIC (mandatory), comfortable clothes and joggers, a light jacket or shawl, an umbrella (optional), personal hygiene items and medicine.",
		),
		array(
			'title' => 'Couple Trips (Married Couples Only)', 'dest' => array( 'Hunza', 'Skardu', 'Swat Kalam', 'Kashmir' ), 'types' => array( 'Couple Trip', 'Private Tour', 'Customized Tour' ), 'duration' => 'Plan to your budget', 'featured' => 0,
			'price' => 22000, 'price_label' => 'starting price',
			'excerpt' => 'Couple trips starting from Rs. 22,000. Get your trip according to your budget.',
			'content' => 'Couple trips for married couples only, with offers for Hunza Valley, Skardu Valley, Swat Valley, Neelum Valley, Gilgit-Baltistan and many more places. Tell us your budget and dates and we plan the trip around them.',
			'days' => array( 'Plan together' => array( 'Tell us the destination, number of days and your budget on WhatsApp or in the booking form', 'We send you a plan and the price before you pay anything' ) ),
			'extra_info' => "Couple trips are for married couples only.\nProof of Nikah Nama (marriage certificate) is required for all couple bookings.",
		),
	);
}

function kv_real_faqs() {
	return array(
		array( 'How do I book a trip?', 'Choose a tour and send the booking form, or message us on WhatsApp. Our team contacts you to confirm the details. The advance payment is Rs. 3,000 per person and the remaining amount is paid at the time of departure.' ),
		array( 'Where do your group trips start?', 'Most group trips leave from Lahore and Faisalabad at 10:00 PM. Members from Islamabad are picked up on Day 1. The Naran tour runs every Thursday and Monday night from Faisalabad, Lahore and Islamabad.' ),
		array( 'Do you offer couple trips?', 'Yes, couple trips start from Rs. 22,000 and are planned to your budget. They are for married couples only and a copy of the Nikah Nama is required.' ),
		array( 'Do you book hotel rooms?', 'Yes. We can arrange rooms in Murree, Naran Kaghan, Neelum Valley, Hunza, Skardu and Swat. Please confirm 4 to 5 days before you need the room.' ),
		array( 'What is your cancellation policy?', '50% is deducted if you inform us 7 days before the trip, 75% if you inform us 3 days before the trip, and 100% if you inform us less than 3 days before the trip. No amount is refunded if a traveller leaves the trip at any stage for any reason. Please read the full Cancellation Policy page.' ),
		array( 'Can I choose my seat?', 'Seats are allotted before departure and shuffled during the trip. If you want a specific seat, Rs. 1,000 per seat per day is charged.' ),
		array( 'Can I bring children?', 'Yes. Children under 3 years travel free (on lap only) and children of 8 years and above pay full charges. Please contact us for children between 3 and 7 years. Parents or guardians are responsible for their children during the trip.' ),
		array( 'What should I bring?', 'Your original CNIC is mandatory. Also bring comfortable clothes and joggers, a light jacket or shawl, an umbrella (optional), personal hygiene items and your medicine. See the Terms and Conditions for the full list.' ),
		array( 'Are the trips insured?', 'No. The trips are not insured. We take all precautions, but travellers are responsible for their own travel or health insurance if they want it.' ),
	);
}

function kv_real_policy_pages() {
	return array(
		'terms-and-conditions' => '<p>Please read these terms before booking a trip with Kashmiri Ventures.</p><ol>'
			. '<li><strong>Health &amp; Safety:</strong> Always carry a hand sanitizer and use it frequently. Wearing a face mask is mandatory. If you feel unwell (fever, cough, sneezing), do not join the tour, for your safety and the safety of others.</li>'
			. '<li><strong>Travel Essentials:</strong> Participants must carry: original CNIC (mandatory), masks, gloves, sanitizer, day bag, raincoat, water bottle, warm clothes, sunblock, sunglasses, power bank and torch.</li>'
			. '<li><strong>Personal Medications:</strong> Bring enough medication to last the entire trip. Inform us of any medical conditions that may require special attention.</li>'
			. '<li><strong>Behavior &amp; Prohibited Items:</strong> Participants must maintain respectful behavior at all times. Any unethical behavior, including misconduct with locals or group members, use of abusive language, alcohol, drugs, or carrying weapons, will result in immediate removal from the tour without a refund.</li>'
			. '<li><strong>Vehicle Use:</strong> On mountainous roads, the vehicle\'s AC or heater will be used intermittently to prevent the vehicle from overheating and to ensure optimal performance.</li>'
			. '<li><strong>Mechanical Issues:</strong> In case of a mechanical issue with the vehicle, efforts will be made to fix it promptly. However, if an extended stay becomes necessary, clients will bear the cost of additional accommodation and meals.</li>'
			. '<li><strong>Liability:</strong> While every precaution is taken for your safety, there are risks involved, including natural disasters such as avalanches, rockslides, or accidents. The company, group leader and tour organizers will not be held responsible for any such incidents.</li>'
			. '<li><strong>Insurance:</strong> The trips are not insured. Participants are responsible for obtaining their own travel or health insurance if required.</li>'
			. '<li><strong>Personal Belongings:</strong> We are not responsible for any theft, loss or damage to personal belongings during the trip. Misplacement is the individual\'s responsibility.</li>'
			. '<li><strong>Respect for Local Culture:</strong> Show respect for local customs, traditions and the environment. Actions that disrespect local culture or damage the environment will not be tolerated.</li>'
			. '<li><strong>Punctuality &amp; Group Harmony:</strong> Participants are required to be on time for all departures and activities. Delays can affect the entire group. Participants must avoid actions that disturb group dynamics, including excessive noise or non-cooperation.</li>'
			. '<li><strong>Accommodation &amp; Meals:</strong> Accommodation and meals provided during the trip will be as per the itinerary. Special requests may be accommodated depending on availability but are not guaranteed. Room sharing will be 4 to 5 persons per room, and only legally married couples will be provided with a separate room.</li>'
			. '<li><strong>Unmarried Couples Policy:</strong> We do not facilitate unmarried couples. If you provide fake details to book a trip as a couple, strict action will be taken against you as per Pakistan\'s legal framework. Proof of Nikah Nama (marriage certificate) is required for all couple bookings.</li>'
			. '<li><strong>Itinerary Changes:</strong> The itinerary is subject to change due to unforeseen circumstances such as weather conditions, road closures or local events. These changes are made in the interest of the group\'s safety and overall experience.</li>'
			. '<li><strong>Photography &amp; Media:</strong> The company reserves the right to use photographs or videos taken during the trip for promotional purposes. If you do not wish to be included, please inform us in advance.</li>'
			. '<li><strong>Emergency Contact:</strong> Participants must provide an emergency contact number before the trip.</li>'
			. '<li><strong>Children:</strong> Parents or guardians are fully responsible for the safety and behavior of their children during the trip.</li>'
			. '<li><strong>Cold Weather Arrangements:</strong> Due to extreme cold weather, a jeep may be required at certain locations, and the cost will be borne by the customer. Water in the taps may freeze at night. In such cases, hot water may be provided in buckets, or alternative arrangements will be made.</li>'
			. '<li><strong>Cancellation Policy:</strong> The advance payment is non-refundable and non-adjustable. If you skip the trip on the spot, you are liable to pay the full trip cost. If you cancel less than 7 days before the trip, the cancellation policy will be applied.</li>'
			. '<li><strong>Seats Arrangement Policy:</strong> Seats are allocated before departure and shuffled during the trip. If you prefer a specific seat, an additional Rs. 1,000 per seat per day will be charged.</li>'
			. '</ol>',
		'cancellation-policy' => '<h3>Seat cancellation policy</h3><ul><li><strong>50% deduction</strong> if you inform us of the cancellation 7 days before the trip.</li><li><strong>75% deduction</strong> if you inform us 3 days before the trip.</li><li><strong>100% deduction</strong> if you inform us less than 3 days before the trip.</li></ul><p>No amount will be refunded if any person leaves the trip at any stage, for any reason.</p><p>The advance payment is non-refundable and non-adjustable. If you skip the trip on the spot, you are liable to pay the full trip cost.</p><p>To cancel, tell us in writing by WhatsApp or email as early as possible.</p>',
		'refund-policy' => '<h3>Refunds</h3><p>Refunds follow the deductions in our <a href="' . esc_url( kv_page_url( 'cancellation-policy' ) ) . '">Cancellation Policy</a>: 50% is deducted if you inform us 7 days before the trip, 75% if you inform us 3 days before the trip and 100% if you inform us less than 3 days before the trip.</p><p>The advance payment is non-refundable and non-adjustable. No amount is refunded if a traveller leaves the trip at any stage for any reason, or is removed from the tour for misconduct.</p>',
		'booking-policy' => '<h3>Registration process</h3><ul><li>Advance payment: <strong>Rs. 3,000 per person</strong>.</li><li>The remaining amount is paid at the time of departure.</li></ul><h3>Seats</h3><p>Seats are allotted before departure and shuffled during the trip. If you prefer a specific seat, an additional Rs. 1,000 per seat per day is charged.</p><h3>Children</h3><p>Children under 3 years travel free (on lap only). Children of 8 years and above pay full charges. Parents or guardians are responsible for their children during the trip.</p><h3>Couples</h3><p>Couple trips are for married couples only. Proof of Nikah Nama (marriage certificate) is required for all couple bookings.</p><h3>Room sharing</h3><p>Room sharing is 4 to 5 persons per room, except couple bookings, which get a separate room.</p><p>Please also read our <a href="' . esc_url( kv_page_url( 'terms-and-conditions' ) ) . '">Terms and Conditions</a> and <a href="' . esc_url( kv_page_url( 'cancellation-policy' ) ) . '">Cancellation Policy</a>.</p>',
	);
}

function kv_import_real_content() {
	// Replace the sample (demo) items.
	$demo = get_posts( array( 'post_type' => array( 'kv_tour', 'kv_review', 'kv_gallery', 'kv_faq' ), 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_kv_demo', 'meta_value' => '1' ) );
	foreach ( $demo as $id ) {
		wp_delete_post( $id, true );
	}

	kv_seed_destinations();
	$k = get_term_by( 'name', 'Kalash Valley', 'kv_destination' );
	if ( $k && 0 === (int) $k->count ) {
		wp_delete_term( $k->term_id, 'kv_destination' );
	}
	foreach ( array( 'Couple Trip' ) as $t ) {
		if ( ! term_exists( $t, 'kv_tour_type' ) ) {
			wp_insert_term( $t, 'kv_tour_type' );
		}
	}

	foreach ( kv_real_tours() as $t ) {
		$exists = get_posts( array( 'post_type' => 'kv_tour', 'title' => $t['title'], 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) );
		if ( $exists ) {
			continue;
		}
		$id = wp_insert_post( array( 'post_type' => 'kv_tour', 'post_status' => 'publish', 'post_title' => $t['title'], 'post_excerpt' => $t['excerpt'], 'post_content' => $t['content'] ) );
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		wp_set_object_terms( $id, $t['dest'], 'kv_destination' );
		wp_set_object_terms( $id, $t['types'], 'kv_tour_type' );
		$info = "Group trips leave from Lahore / Faisalabad at 10:00 PM. Members from Islamabad are picked up on Day 1.\nOriginal CNIC is mandatory for every traveller.\nThe itinerary can change because of weather, road closures or local events.\nPlease read the Terms and Conditions and the Cancellation Policy before booking.";
		if ( ! empty( $t['extra_info'] ) ) {
			$info = $t['extra_info'] . "\n" . $info;
		}
		if ( isset( $t['price'] ) ) {
			$info = "Couple trips are for married couples only.\nProof of Nikah Nama (marriage certificate) is required for all couple bookings.\nPlease read the Terms and Conditions and the Cancellation Policy before booking.";
		}
		update_post_meta( $id, '_kv_price', isset( $t['price'] ) ? $t['price'] : 0 );
		update_post_meta( $id, '_kv_child_price', '' );
		update_post_meta( $id, '_kv_price_label', isset( $t['price_label'] ) ? $t['price_label'] : 'per person' );
		update_post_meta( $id, '_kv_duration', $t['duration'] );
		update_post_meta( $id, '_kv_featured', $t['featured'] ? '1' : '0' );
		update_post_meta( $id, '_kv_departures', array() );
		update_post_meta( $id, '_kv_itinerary', kv_days( $t['days'] ) );
		update_post_meta( $id, '_kv_includes', isset( $t['includes'] ) ? $t['includes'] : '' );
		update_post_meta( $id, '_kv_excludes', isset( $t['excludes'] ) ? $t['excludes'] : '' );
		update_post_meta( $id, '_kv_info', $info );
		update_post_meta( $id, '_kv_faqs', array() );
		update_post_meta( $id, '_kv_real', '1' );
	}

	$has_faq = get_posts( array( 'post_type' => 'kv_faq', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) );
	if ( ! $has_faq ) {
		foreach ( kv_real_faqs() as $i => $f ) {
			wp_insert_post( array( 'post_type' => 'kv_faq', 'post_status' => 'publish', 'post_title' => $f[0], 'post_content' => $f[1], 'menu_order' => $i ) );
		}
	}

	// Policy pages: replace only pages that still hold the original draft text.
	foreach ( kv_real_policy_pages() as $slug => $html ) {
		$p = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $p && false !== strpos( $p->post_content, 'Draft text. Please review' ) ) {
			wp_update_post( array( 'ID' => $p->ID, 'post_content' => $html ) );
		}
	}

	// About text: replace only the original placeholder.
	$s = get_option( 'kv_settings', array() );
	$s = is_array( $s ) ? $s : array();
	if ( empty( $s['about_text'] ) || false !== strpos( $s['about_text'], 'This text is a placeholder' ) ) {
		$d             = kv_defaults();
		$s['about_text'] = $d['about_text'];
		update_option( 'kv_settings', $s );
	}
	update_option( 'kv_real_imported', '1' );
}

add_action( 'admin_post_kv_import_real', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You are not allowed to do this.' );
	}
	check_admin_referer( 'kv_demo' );
	kv_import_real_content();
	kv_flash( 'success', 'Kashmiri Ventures tours, FAQs and policies imported. Demo items were removed.' );
	wp_safe_redirect( admin_url( 'admin.php?page=kv-settings' ) );
	exit;
} );
