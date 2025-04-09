<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bus_booking";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and sanitize input data
$required_fields = ['bus_id', 'bus_name', 'departure_date', 'source', 'destination', 
                   'passenger_name', 'passenger_age', 'passenger_gender', 
                   'passenger_mobile', 'seat_numbers', 'total_price'];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        die("Error: Required field '$field' is missing");
    }
}

// Sanitize inputs
$bus_id = $conn->real_escape_string($_POST['bus_id']);
$bus_name = $conn->real_escape_string($_POST['bus_name']);
$passenger_name = $conn->real_escape_string($_POST['passenger_name']);
$passenger_age = $conn->real_escape_string($_POST['passenger_age']);
$passenger_gender = $conn->real_escape_string($_POST['passenger_gender']);
$passenger_mobile = $conn->real_escape_string($_POST['passenger_mobile']);
$alternate_mobile = isset($_POST['alternate_mobile']) ? $conn->real_escape_string($_POST['alternate_mobile']) : '';
$seat_numbers = $conn->real_escape_string($_POST['seat_numbers']);
$total_price = $conn->real_escape_string($_POST['total_price']);
$source = $conn->real_escape_string($_POST['source']);
$destination = $conn->real_escape_string($_POST['destination']);

// Convert date format
$departure_date = DateTime::createFromFormat('d-m-Y', $_POST['departure_date']);
if (!$departure_date) {
    die("Invalid date format. Please use dd-mm-yyyy format.");
}
$departure_date = $departure_date->format('Y-m-d');

// Generate booking ID
$booking_id = 'BK' . uniqid();

// Insert booking
$sql = "INSERT INTO bookings (
    booking_id, 
    bus_id, 
    bus_name, 
    departure_date, 
    source, 
    destination, 
    passenger_name, 
    passenger_age, 
    passenger_gender, 
    passenger_mobile, 
    alternate_mobile, 
    seat_numbers, 
    total_price, 
    booking_date
) VALUES (
    '$booking_id',
    '$bus_id',
    '$bus_name',
    '$departure_date',
    '$source',
    '$destination',
    '$passenger_name',
    '$passenger_age',
    '$passenger_gender',
    '$passenger_mobile',
    '$alternate_mobile',
    '$seat_numbers',
    '$total_price',
    NOW()
)";

if ($conn->query($sql)) {
    header("Location: payment.php?booking_id=$booking_id");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>