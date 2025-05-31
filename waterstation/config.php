<?php
// config.php - database connection file for local development

$servername = "localhost";
$username = "root";    // Default local MySQL username
$password = "";        // Default local MySQL password is blank
$dbname = "water_station";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
