<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "bus_booking");

// Get booking ID from URL
$booking_id = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : null;
$booking = null;
$error = '';
$success = '';

// Fetch booking details
if ($booking_id) {
    $booking = $conn->query("SELECT * FROM bookings WHERE booking_id = '$booking_id'")->fetch_assoc();
    if (!$booking) {
        $error = "Booking not found";
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_booking'])) {

    $bus_name = $conn->real_escape_string($_POST['bus_name']);
    $source = $conn->real_escape_string($_POST['source']);
    $destination = $conn->real_escape_string($_POST['destination']);
    $departure_date = $conn->real_escape_string($_POST['departure_date']);
  
    $seat_numbers = $conn->real_escape_string($_POST['seat_numbers']);
    $total_price = $conn->real_escape_string($_POST['total_price']);
    $payment_status = $conn->real_escape_string($_POST['payment_status']);
    
    $update_query = $conn->query("UPDATE bookings SET 

                 bus_name = '$bus_name',
                 source = '$source',
                 destination = '$destination',
                 departure_date = '$departure_date',
           
                 seat_numbers = '$seat_numbers',
                 total_price = '$total_price',
                 payment_status = '$payment_status'
                 WHERE booking_id = '$booking_id'");
    
    if ($update_query) {
        $success = "Booking updated successfully!";
        $booking = $conn->query("SELECT * FROM bookings WHERE booking_id = '$booking_id'")->fetch_assoc();
    } else {
        $error = "Error updating booking: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #6a11cb;
            --secondary-color: #2575fc;
            --danger-color: #ff4d4d;
            --success-color: #4CAF50;
            --warning-color: #ff9800;
            --dark-color: #333;
            --light-color: #f9f9f9;
            --gray-color: #ddd;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: var(--dark-color);
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: 100%;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 14px;
            opacity: 0.8;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .menu-item:hover, .menu-item.active {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .menu-item i {
            margin-right: 10px;
            font-size: 18px;
        }

        .menu-item a {
            color: white;
            text-decoration: none;
            display: block;
            width: 100%;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .logout-btn {
            background-color: var(--danger-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .logout-btn:hover {
            background-color: #e03e3e;
        }

        /* Card Styles */
        .card {
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 18px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--gray-color);
            color: var(--primary-color);
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group input[type="time"],
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(37, 117, 252, 0.2);
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        /* Status Select */
        .payment-status-select {
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            width: 100%;
            font-weight: 500;
        }

        .payment-status-select.paid {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }

        .payment-status-select.pending {
            background-color: rgba(255, 152, 0, 0.1);
            color: var(--warning-color);
        }

        /* Button Styles */
        .btn {
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 14px;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #1a68e0;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: var(--gray-color);
            color: var(--dark-color);
        }

        .btn-secondary:hover {
            background-color: #ccc;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        /* Messages */
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* No Records */
        .no-records {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .no-records i {
            font-size: 50px;
            margin-bottom: 20px;
            color: #ddd;
        }

        .no-records p {
            font-size: 18px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Bus Booking System</h2>
                <p>Admin Panel</p>
            </div>
            <div class="sidebar-menu">
                <div class="menu-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <a href="admin_dashboard.php">Dashboard</a>
                </div>
                <div class="menu-item">
                    <i class="fas fa-calendar-alt"></i>
                    <a href="manage_bookings.php">Bookings</a>
                </div>
                <div class="menu-item">
                    <i class="fas fa-money-bill-wave"></i>
                    <a href="manage_payments.php">Payments</a>
                </div>
                <div class="menu-item">
                    <i class="fas fa-cog"></i>
                    <a href="settings.php">Settings</a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h2>Edit Booking</h2>
                <div class="user-info">
                    <img src="admin.png" alt="Admin">
                    <span><?php echo $_SESSION['admin_username']; ?></span>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            
            <div class="card">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($booking): ?>
                <form method="POST">
                    <h3 class="section-title">Booking Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="booking_id">Booking ID</label>
                            <input type="text" id="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="booking_date">Booking Date</label>
                            <input type="text" id="booking_date" value="<?php echo date('d M Y', strtotime($booking['booking_date'])); ?>" readonly>
                        </div>
                    </div>
                    
    
                    
             
                    
                    <h3 class="section-title">Trip Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bus_name">Bus Name</label>
                            <input type="text" id="bus_name" name="bus_name" value="<?php echo htmlspecialchars($booking['bus_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="source">Source</label>
                            <input type="text" id="source" name="source" value="<?php echo htmlspecialchars($booking['source']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="destination">Destination</label>
                            <input type="text" id="destination" name="destination" value="<?php echo htmlspecialchars($booking['destination']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="departure_date">Departure Date</label>
                            <input type="date" id="departure_date" name="departure_date" value="<?php echo htmlspecialchars($booking['departure_date']); ?>" required>
                        </div>
                    </div>
           
                    
                    <h3 class="section-title">Booking Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="seat_numbers">Seat Numbers</label>
                            <input type="text" id="seat_numbers" name="seat_numbers" value="<?php echo htmlspecialchars($booking['seat_numbers']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="total_price">Total Price (₹)</label>
                            <input type="number" step="0.01" id="total_price" name="total_price" value="<?php echo htmlspecialchars($booking['total_price']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="payment_status">Payment Status</label>
                            <select id="payment_status" name="payment_status" class="payment-status-select <?php echo $booking['payment_status']; ?>">
                                <option value="paid" <?php echo $booking['payment_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                <option value="pending" <?php echo $booking['payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" name="update_booking" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Booking
                        </button>
                        <a href="manage_bookings.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
                <?php else: ?>
                    <div class="no-records">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Booking not found or invalid booking ID</p>
                        <a href="manage_bookings.php" class="btn btn-secondary" style="margin-top: 20px;">
                            <i class="fas fa-arrow-left"></i> Back to Bookings
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Dynamically update payment status select styling
        document.getElementById('payment_status').addEventListener('change', function() {
            this.className = 'payment-status-select ' + this.value;
        });
    </script>
</body>
</html>