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

// Function to sanitize input data
function sanitizeInput($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

// Check if we're coming from seat selection with booking data
if (isset($_GET['booking_id'])) {
    // Get booking details from database
    $booking_id = sanitizeInput($_GET['booking_id']);
    $sql = "SELECT * FROM bookings WHERE booking_id = '$booking_id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        
        // Redirect to payment page with booking details
        $redirect_url = "payment.html?" . http_build_query([
            'bookingId' => $booking['booking_id'],
            'passengerName' => urlencode($booking['passenger_name']),
            'seatNumbers' => urlencode($booking['seat_numbers']),
            'totalPrice' => $booking['total_price'],
            'busName' => urlencode($booking['bus_name']),
            'departureDate' => date('d-m-Y', strtotime($booking['departure_date'])),
            'source' => urlencode($booking['source']),
            'destination' => urlencode($booking['destination'])
        ]);
        
        header("Location: $redirect_url");
        exit();
    } else {
        die("Booking not found");
    }
}

// Handle payment confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $required_fields = [
        'bus_id', 'bus_name', 'departure_date', 'source', 'destination',
        'passenger_name', 'passenger_age', 'passenger_gender', 
        'passenger_mobile', 'seat_numbers', 'total_price'
    ];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            die(json_encode(['status' => 'error', 'message' => "Missing required field: $field"]));
        }
    }
    
    // Extract and sanitize data
    $bus_id = sanitizeInput($_POST['bus_id']);
    $bus_name = sanitizeInput($_POST['bus_name']);
    $departure_date = sanitizeInput($_POST['departure_date']);
    $source = sanitizeInput($_POST['source']);
    $destination = sanitizeInput($_POST['destination']);
    $passenger_name = sanitizeInput($_POST['passenger_name']);
    $passenger_age = intval($_POST['passenger_age']);
    $passenger_gender = sanitizeInput($_POST['passenger_gender']);
    $passenger_mobile = sanitizeInput($_POST['passenger_mobile']);
    $alternate_mobile = isset($_POST['alternate_mobile']) ? sanitizeInput($_POST['alternate_mobile']) : '';
    $seat_numbers = sanitizeInput($_POST['seat_numbers']);
    $total_price = floatval($_POST['total_price']);
    
    // Validate mobile numbers
    if (!preg_match('/^[0-9]{10}$/', $passenger_mobile)) {
        die(json_encode(['status' => 'error', 'message' => "Invalid mobile number format"]));
    }
    
    if ($alternate_mobile && !preg_match('/^[0-9]{10}$/', $alternate_mobile)) {
        die(json_encode(['status' => 'error', 'message' => "Invalid alternate mobile number format"]));
    }
    
    // Convert date format from d-m-Y to Y-m-d if needed
    $departure_date_obj = DateTime::createFromFormat('d-m-Y', $departure_date);
    if ($departure_date_obj) {
        $departure_date = $departure_date_obj->format('Y-m-d');
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Generate booking ID
        $booking_id = 'BK' . uniqid();
        
        // Insert booking record
        $booking_sql = "INSERT INTO bookings (
            booking_id,
            bus_id, bus_name, departure_date, source, destination,
            passenger_name, passenger_age, passenger_gender, passenger_mobile,
            alternate_mobile, seat_numbers, total_price, booking_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($booking_sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $bind_result = $stmt->bind_param(
            "sissssissssd",
            $booking_id,
            $bus_id, $bus_name, $departure_date, $source, $destination,
            $passenger_name, $passenger_age, $passenger_gender, $passenger_mobile,
            $alternate_mobile, $seat_numbers, $total_price
        );
        
        if ($bind_result === false) {
            throw new Exception("Bind failed: " . $stmt->error);
        }
        
        $execute_result = $stmt->execute();
        if ($execute_result === false) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        // Redirect to payment page with booking details
        $redirect_url = "payment.html?" . http_build_query([
            'bookingId' => $booking_id,
            'passengerName' => urlencode($passenger_name),
            'seatNumbers' => urlencode($seat_numbers),
            'totalPrice' => $total_price,
            'busName' => urlencode($bus_name),
            'departureDate' => $_POST['departure_date'],
            'source' => urlencode($source),
            'destination' => urlencode($destination)
        ]);
        
        $conn->commit();
        header("Location: $redirect_url");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        die(json_encode(['status' => 'error', 'message' => "Error processing booking: " . $e->getMessage()]));
    }
}

// Handle payment confirmation from payment.html
if (isset($_GET['confirm_payment'])) {
    $transaction_id = sanitizeInput($_GET['transaction_id']);
    $booking_id = sanitizeInput($_GET['booking_id']);
    
    if (empty($transaction_id)) {
        die(json_encode(['status' => 'error', 'message' => 'Transaction ID is required']));
    }
    
    try {
        // Update booking with payment status
        $update_sql = "UPDATE bookings SET 
            payment_status = 'paid',
            transaction_id = ?,
            payment_date = NOW()
            WHERE booking_id = ?";
        
        $stmt = $conn->prepare($update_sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $bind_result = $stmt->bind_param("ss", $transaction_id, $booking_id);
        if ($bind_result === false) {
            throw new Exception("Bind failed: " . $stmt->error);
        }
        
        $execute_result = $stmt->execute();
        if ($execute_result === false) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Payment confirmed successfully']);
        
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => "Error confirming payment: " . $e->getMessage()]);
    }
    
    exit();
}
?>