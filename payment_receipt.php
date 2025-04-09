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
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #6a11cb;
            padding-bottom: 20px;
        }
        
        .receipt-header h1 {
            color: #6a11cb;
            margin: 0;
        }
        
        .receipt-header p {
            color: #666;
            margin: 5px 0 0;
        }
        
        .receipt-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .detail-group h3 {
            color: #6a11cb;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .detail-label {
            width: 150px;
            font-weight: 600;
            color: #555;
        }
        
        .receipt-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #6a11cb;
        }
        
        .print-btn {
            background: #6a11cb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        .sidebar a:link,
.sidebar a:visited,
.sidebar a:hover,
.sidebar a:active {
    color: white;
    text-decoration: none;
}

.sidebar li:hover {
    background-color: #34495e;
}

.sidebar li.active {
    background-color: #3498db;
}

.sidebar li.active a {
    color: white;
}
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>Bus Booking System</h1>
            <p>Payment Receipt</p>
        </div>
        
        <div class="receipt-details">
            <div class="detail-group">
                <h3>Booking Information</h3>
                <div class="detail-row">
                    <div class="detail-label">Booking ID:</div>
                    <div><?= $booking['booking_id'] ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Date:</div>
                    <div><?= date('d M Y', strtotime($booking['booking_date'])) ?></div>
                </div>
                <div class="detail-row">
                    <div