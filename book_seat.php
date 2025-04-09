<?php
// Set headers first to ensure proper content type
header('Content-Type: application/json');

// Database configuration
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "bus_booking";

// Create response array
$response = ['success' => false, 'message' => ''];

try {
    // Get and validate input data
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input === null) {
        throw new Exception("Invalid JSON input");
    }

    // Validate required fields
    $requiredFields = ['bus_id', 'seat_number', 'name', 'age', 'gender', 'mobile', 'date'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Create database connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Check if seat is already booked
    $checkStmt = $conn->prepare("SELECT id FROM bookings WHERE bus_id = ? AND seat_number = ? AND date = ?");
    $checkStmt->bind_param("iis", $input['bus_id'], $input['seat_number'], $input['date']);
    $checkStmt->execute();
    
    if ($checkStmt->get_result()->num_rows > 0) {
        throw new Exception("Seat {$input['seat_number']} is already booked");
    }

    // Insert booking
    <?php
    // Database connection
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
    
    // Verify the bus exists first
    $bus_id = 1; // Change this to an existing bus ID
    $check_bus = $conn->query("SELECT id FROM buses WHERE id = $bus_id");
    
    if ($check_bus->num_rows == 0) {
        die("Error: Bus with ID $bus_id does not exist");
    }
    
    // Prepare the INSERT statement
    $stmt = $conn->prepare("INSERT INTO bookings (
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
        payment_status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed')");
    
    // Bind parameters
    $stmt->bind_param("isssssissssd", 
        $bus_id,
        $bus_name = "Express Deluxe",
        $departure_date = "2023-12-15",
        $source = "New York",
        $destination = "Boston",
        $passenger_name = "John Doe",
        $passenger_age = 35,
        $passenger_gender = "male",
        $passenger_mobile = "1234567890",
        $alternate_mobile = NULL,
        $seat_numbers = "12,13",
        $total_price = 100.00
    );
    
    // Execute and check for errors
    if ($stmt->execute()) {
        echo "Booking created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
    ?>
    