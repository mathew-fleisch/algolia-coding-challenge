<?php
// $dbhost = '216.97.233.20';
$dbhost = 'localhost';
$dbuser = 'digit26_alguser';
$dbpass = 'algP@ssw0rd';
$dbname = 'digit26_algolia';
$dbport = '3306';
$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>