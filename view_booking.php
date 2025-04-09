<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_bookings.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "bus_booking");
$booking_id = $conn->real_escape_string($_GET['id']);
$booking = $conn->query("SELECT * FROM bookings WHERE booking_id = '$booking_id'")->fetch_assoc();

if (!$booking) {
    header("Location: manage_bookings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Same styling as admin_dashboard.php */
        .booking-details {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-label {
            width: 200px;
            font-weight: 600;
            color: #555;
        }
        
        .detail-value {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar same as dashboard -->
        
        <div class="main-content">
            <!-- Header same as dashboard -->
            
            <div class="booking-details">
                <h3 class="section-title">Booking Details</h3>
                
                <div class="detail-row">
                    <div class="detail-label">Booking ID:</div>
                    <div class="detail-value"><?= $booking['booking_id'] ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Passenger Name:</div>
                    <div class="detail-value"><?= $booking['passenger_name'] ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Bus Name:</div>
                    <div class="detail-value"><?= $booking['bus_name'] ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Departure Date:</div>
                    <div class="detail-value"><?= date('d M Y', strtotime($booking['departure_date'])) ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Route:</div>
                    <div class="detail-value"><?= $booking['source'] ?> to <?= $booking['destination'] ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Seat Numbers:</div>
                    <div class="detail-value"><?= $booking['seat_numbers'] ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Total Price:</div>
                    <div class="detail-value">₹<?= number_format($booking['total_price'], 2) ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Payment Status:</div>
                    <div class="detail-value">
                        <span class="status <?= $booking['payment_status'] === 'paid' ? 'status-paid' : 'status-pending' ?>">
                            <?= ucfirst($booking['payment_status']) ?>
                        </span>
                    </div>
                </div>
                
                <?php if ($booking['payment_status'] === 'paid'): ?>
                <div class="detail-row">
                    <div class="detail-label">Transaction ID:</div>
                    <div class="detail-value"><?= $booking['transaction_id'] ?></div>
                </div>
                <?php endif; ?>
                
                <div class="detail-row">
                    <div class="detail-label">Booking Date:</div>
                    <div class="detail-value"><?= date('d M Y H:i', strtotime($booking['booking_date'])) ?></div>
                </div>
                
                <div class="action-buttons" style="margin-top: 20px;">
                    <a href="manage_bookings.php" class="action-btn view-btn">Back to Bookings</a>
                    <a href="edit_booking.php?id=<?= $booking['booking_id'] ?>" class="action-btn edit-btn">Edit</a>
                    <a href="payment_receipt.php?id=<?= $booking['booking_id'] ?>" class="action-btn verify-btn">View Receipt</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>