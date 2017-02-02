<?php
include '/Users/rush/Desktop/Algolia/MathewFleisch/inc/config.php';

$json_string = file_get_contents("dataset/restaurants_list.json");
$json = json_decode($json_string, true);


if($argv[1]) { $page_size = (int)$argv[1]; } else { $page_size = 5000; }
if($argv[2]) { $offset = (int)$argv[2]; } else { $offset = 0; }
echo "Importing restaurants_list.json...\n";
echo "Page size: $page_size\n";
echo "Offset: $offset\n";
sleep(1);

// print_r($json);

if(count($json) > 0) {
	$add_restaurant = $conn->prepare("INSERT INTO restaurants "
		."(objectID, name, phone, address, area, city, state, country, postal_code, lat, lng, price, payment_options)"
		."VALUES "
		."(?,?,?,?,?,?,?,?,?,?,?,?,?)");
	$add_restaurant->bind_param("issssssssddis", $objectID, $name, $phone, $address, $area, $city, $state, $country, $postal_code, $lat, $lng, $price, $payment_options);


	$get_payment_options = $conn->prepare("SELECT id, name FROM payment_options");
	$get_payment_options->execute();
	$res = $get_payment_options->get_result();
	$default_payment_options = array();
	while($r = $res->fetch_assoc()) {
		$default_payment_options[$r['name']] = $r['id'];
	}

	foreach($json as $tkey=>$restarant) {
		if($tkey >= ($page_size*$offset) && $tkey < ($page_size*($offset+1))) {
			if(array_key_exists('objectID', $restarant)) {
				$objectID = $restarant['objectID'];
			} else { $objectID = null; }
			if(array_key_exists('name', $restarant)) {
				$name = $restarant['name'];
			} else { $name = null; }
			if(array_key_exists('phone', $restarant)) {
				$phone = preg_replace("/[^0-9x]/", "", preg_replace("/x$/", "", $restarant['phone']));
			} else { $phone = null; }
			if(array_key_exists('address', $restarant)) {
				$address = $restarant['address'];
			} else { $address = null; }
			if(array_key_exists('area', $restarant)) {
				$area = $restarant['area'];
			} else { $area = null; }
			if(array_key_exists('city', $restarant)) {
				$city = $restarant['city'];
			} else { $city = null; }
			if(array_key_exists('state', $restarant)) {
				$state = $restarant['state'];
			} else { $state = null; }
			if(array_key_exists('country', $restarant)) {
				$country = $restarant['country'];
			} else { $country = null; }
			if(array_key_exists('postal_code', $restarant)) {
				$postal_code = $restarant['postal_code'];
			} else { $postal_code = null; }
			if(array_key_exists('price', $restarant)) {
				$price = $restarant['price'];
			} else { $price = null; }
			if(array_key_exists('_geoloc', $restarant)) {
				if(array_key_exists('lat', $restarant['_geoloc'])) {
					$lat = $restarant['_geoloc']['lat'];
				} else { $lat = null; }
				if(array_key_exists('lng', $restarant['_geoloc'])) {
					$lng = $restarant['_geoloc']['lng'];
				} else { $lng = null; }
			} else { $lat = null; $lng = null; }

			if(array_key_exists('payment_options', $restarant)) {
				$payment_options = implode(",", array_keys(process_payment_options($conn, $default_payment_options, $restarant['payment_options'])));
			}



			if(is_int($objectID) && $objectID > 0) {
				// print_r($restarant);
				echo "\n------------------$tkey------------------\n";
				echo "objectID:$objectID\n";
				echo "name:$name\n";
				echo "phone:$phone\n";
				echo "address:$address\n";
				echo "area:$area\n";
				echo "city:$city\n";
				echo "state:$state\n";
				echo "country:$country\n";
				echo "postal_code:$postal_code\n";
				echo "price:$price\n";
				echo "lat:$lat\n";
				echo "lng:$lng\n";
				echo "Payment Options: $payment_options\n";


				$add_restaurant->execute();
			}
		}
	}
} else { echo "There are no restaurants in this json file...\n"; }


function process_payment_options($conn, $default_payment_options, $options) {
	// print_r($options);
	// print_r($default_payment_options);
	$ret = array();
	if(array_key_exists($card, $default_payment_options)) { 
		$ret[$default_payment_options[$card]] = $card;
	} else {
		$getid = $conn->prepare("SELECT id, name FROM payment_options WHERE name = ?");
		$getid->bind_param("s", $card);
		foreach($options as $card) { 
			$getid->execute();
			$result = $getid->get_result();
			$num_of_rows = $result->num_rows;
			if($num_of_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$ret[$row['id']] = $row['name'];
				}
			} else { 
				$put = $conn->prepare("INSERT INTO payment_options (name) VALUES (?)");
				$put->bind_param("s", $card);
				$put->execute();

				$tgetid = $conn->prepare("SELECT id, name FROM payment_options WHERE name = ?");
				$tgetid->bind_param("s", $card);
				$tgetid->execute();
				$result = $tgetid->get_result();
				$num_of_rows = $result->num_rows;
				if($num_of_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$ret[$row['id']] = $row['name'];
					}
				} 			
			}
			$getid->free_result();
		}
		$getid->close();
	}
	return $ret;
}

?>