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

// Function to get all bookings
function getBookings($conn) {
    $sql = "SELECT * FROM bookings";
    $result = $conn->query($sql);
    $bookings = [];
    while($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    return $bookings;
}

// Function to get all buses
function getBuses($conn) {
    $sql = "SELECT * FROM buses";
    $result = $conn->query($sql);
    $buses = [];
    while($row = $result->fetch_assoc()) {
        $buses[] = $row;
    }
    return $buses;
}

// Function to get booked seats for a bus on a specific date
function getBookedSeats($conn, $busId, $date) {
    $sql = "SELECT seat_number FROM bookings WHERE bus_id = ? AND departure_date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $busId, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookedSeats = [];
    while($row = $result->fetch_assoc()) {
        $bookedSeats[] = $row;
    }
    return $bookedSeats;
}

// Function to book a seat
function bookSeat($conn, $data) {
    $sql = "INSERT INTO bookings (bus_id, seat_number, passenger_name, age, gender, mobile, alternate_mobile, departure_date, booking_date, admin_email)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisisssss", 
        $data['bus_id'], 
        $data['seat_number'], 
        $data['name'], 
        $data['age'], 
        $data['gender'], 
        $data['mobile'], 
        $data['alternateMobile'], 
        $data['date'],
        $data['admin_email']
    );
    return $stmt->execute();
}

// Close connection
function closeConnection($conn) {
    $conn->close();
}
?>