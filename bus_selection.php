<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bus_booking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search parameters
$source = $_GET['source'];
$destination = $_GET['destination'];
$departure = $_GET['departure'];

// Query buses
$sql = "SELECT * FROM buses WHERE source='$