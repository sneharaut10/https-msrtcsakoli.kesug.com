<?php
$servername = "localhost";
$username = "root";  // Change this if your DB has a different user
$password = "";  // Set your MySQL password here
$dbname = "payment_db";

// Create Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get Data from Frontend
$upi_id = "9370681450@ptaxis"; // Static UPI ID
$transaction_id = $_POST['transactionId'];
$total_price = $_POST['totalPrice'];

// Prevent Duplicate Transaction ID
$sql_check = "SELECT * FROM payments WHERE transaction_id='$transaction_id'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    echo "Error: Transaction ID already exists!";
    exit();
}

// Insert Data into Database
$sql = "INSERT INTO payments (upi_id, transaction_id, amount) VALUES ('$upi_id', '$transaction_id', '$total_price')";

if ($conn->query($sql) === TRUE) {
    header("Location: payment_success.html?transactionId=$transaction_id&totalPrice=$total_price");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close Connection
$conn->close();
?>
