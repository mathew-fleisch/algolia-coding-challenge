<?php
include 'config.php';
// echo load_restaurant_list($conn, 5000, 0);
// exit();
$valid_action = false;
$default_page_size = 10;
$default_page_offset = 0;

if(isset($_POST['action'])) {
	$action = strip_tags($_POST['action']);
	switch($action) {
		case "load_food_type":
			$valid_action = true;
			echo load_food_type($conn);
		break;
		case "load_payment_options":
			$valid_action = true;
			echo load_payment_options($conn);
		break;
		case "load_restaurant_list":
			$valid_action = true;
			if(isset($_POST['page_size'])) { 
				if(is_int((int)$_POST['page_size'])) { $page_size = (int)$_POST['page_size']; } 
				else { $page_size = $default_page_size; }
			} else { $page_size = $default_page_size; }
			if(isset($_POST['page_offset'])) { 
				if(is_int((int)$_POST['page_offset'])) { $page_offset = (int)$_POST['page_offset']; } 
				else { $page_offset = $default_page_offset; }
			} else { $page_offset = $default_page_offset; }
			echo load_restaurant_list($conn, $page_size, $page_offset);
		break;
	}

	if($valid_action) { exit(); }
}



function load_food_type($conn) { 
	$ret = array();
	$get_food_types = $conn->prepare("SELECT `food_type`, count(`objectID`) AS `count` FROM `restaurants` GROUP BY `food_type` ORDER BY `count` DESC LIMIT 0,7");
	$get_food_types->execute();
	$result = $get_food_types->get_result();
	$num_of_rows = $result->num_rows;
	if($num_of_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$ret[$row['food_type']] = $row['count'];
		}
	}
	$get_food_types->free_result();
	$get_food_types->close();
	return json_encode($ret);
}

function load_payment_options($conn) { 
	$ret = array();
	$get_payment_options = $conn->prepare("SELECT * FROM `payment_options`");
	$get_payment_options->execute();
	$result = $get_payment_options->get_result();
	$num_of_rows = $result->num_rows;
	if($num_of_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$ret[$row['id']] = $row['name'];
		}
	}
	$get_payment_options->free_result();
	$get_payment_options->close();
	return json_encode($ret);
}

function load_restaurant_list($conn, $page_size, $page_offset) {
	$ret = array();
	$get_food_types = $conn->prepare("SELECT * FROM `restaurants` LIMIT ?,?");
	$get_food_types->bind_param("ii", $page_offset, $page_size);
	$get_food_types->execute();
	$result = $get_food_types->get_result();
	$num_of_rows = $result->num_rows;
	if($num_of_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			array_push($ret, $row);
		}
	}
	$get_food_types->free_result();
	$get_food_types->close();
	return json_encode($ret);

}

?>