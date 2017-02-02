<?php

include '/Users/rush/Desktop/Algolia/MathewFleisch/inc/config.php';
$csv = array_map(function($v){return str_getcsv($v, ";");},file('dataset/restaurants_info.csv'));
$headers = $csv[0];
print_r($headers);

sleep(1);
array_shift($csv);

// print_r($csv);

$track = 0;
$update = $conn->prepare("UPDATE restaurants SET food_type = ?, alt_phone = ?, stars_count = ?, reviews_count = ?, neighborhood = ?, price_range = ?, dining_style = ? WHERE objectID = ?");
$update->bind_param("sssisssi", $food_type, $phone, $stars_count, $reviews_count, $neighborhood, $price_range, $dining_style, $objectID);
foreach($csv as $restaurant) {
	/*
	[0] => objectID
	[1] => food_type
	[2] => stars_count
	[3] => reviews_count
	[4] => neighborhood
	[5] => phone_number
	[6] => price_range
	[7] => dining_style
	*/
	$objectID      = $restaurant[0];
	$food_type     = $restaurant[1];
	$stars_count   = $restaurant[2];
	$reviews_count = $restaurant[3];
	$neighborhood  = $restaurant[4];
	$phone         = preg_replace("/[^0-9x]/", "", preg_replace("/x$/", "", $restaurant[5]));
	$price_range   = $restaurant[6];
	$dining_style  = $restaurant[7];
	$update->execute();
	$track++;
	echo "$track (of ".count($csv)."): $objectID updated.\n";
}
$update->close();

?>