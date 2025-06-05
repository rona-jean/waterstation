<?php
$servername = "localhost";
$username = "root";
$password = ""; // default is blank on XAMPP
$dbname = "water_station";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
