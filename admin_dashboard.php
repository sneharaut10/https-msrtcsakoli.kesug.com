<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bus_booking";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_price) as sum FROM bookings WHERE payment_status = 'paid'")->fetch_assoc()['sum'];
$pending_payments = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE payment_status != 'paid'")->fetch_assoc()['count'];
$recent_bookings = $conn->query("SELECT * FROM bookings ORDER BY booking_date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="admin_styles.css">
</head>
<style>
    /* admin_styles.css */
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

/* Common Card Styles */
.card {
    background-color: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
}

.section-title {
    font-size: 18px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--gray-color);
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
.form-group input[type="number"],
.form-group input[type="password"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 14px;
}

.form-group input[type="checkbox"] {
    margin-right: 10px;
}

.btn {
    padding: 10px 20px;
    background-color: var(--secondary-color);
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.3s;
    text-decoration: none;
    display: inline-block;
}

.btn:hover {
    background-color: #1a68e0;
}

.success-message {
    background-color: #d4edda;
    color: #155724;
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 20px;
    border: 1px solid #c3e6cb;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid var(--gray-color);
}

th {
    background-color: #f9f9f9;
    font-weight: 600;
}

.status {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-paid {
    background-color: rgba(76, 175, 80, 0.1);
    color: var(--success-color);
}

.status-pending {
    background-color: rgba(255, 152, 0, 0.1);
    color: var(--warning-color);
}

.action-btn {
    padding: 5px 10px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-size: 12px;
    margin-right: 5px;
    text-decoration: none;
    display: inline-block;
}

.view-btn {
    background-color: rgba(37, 117, 252, 0.1);
    color: var(--secondary-color);
}

.edit-btn {
    background-color: rgba(255, 152, 0, 0.1);
    color: var(--warning-color);
}

.delete-btn {
    background-color: rgba(255, 77, 77, 0.1);
    color: var(--danger-color);
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
}
</style>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Bus Booking System</h2>
                <p>Admin Panel</p>
            </div>
            <div class="sidebar-menu">
                <div class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    <a href="admin_dashboard.php">Dashboard</a>
                </div>
       
      
                <div class="menu-item">
                    <i class="fas fa-calendar-alt"></i>
                    <a href="manage_bookings.php">Bookings</a>
                </div>
                <div class="menu-item">
                    <i class="fas fa-money-bill-wave"></i>
                    <a href="manage_payments.php">Manage Payments</a>
                </div>
           
                <div class="menu-item">
                    <i class="fas fa-cog"></i>
                    <a href="settings.php">Settings</a>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="header">
                <h2>Dashboard</h2>
                <div class="user-info">
                    <img src="admin.png" alt="Admin">
                    <span><?php echo $_SESSION['admin_username']; ?></span>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            
            <div class="stats-container">
                <div class="card">
                    <div class="stat-info">
                        <h3>Total Bookings</h3>
                        <h2><?php echo $total_bookings; ?></h2>
                    </div>
                </div>
                <div class="card">
                    <div class="stat-info">
                        <h3>Total Revenue</h3>
                        <h2>₹<?php echo number_format($total_revenue, 2); ?></h2>
                    </div>
                </div>
                <div class="card">
                    <div class="stat-info">
                        <h3>Pending Payments</h3>
                        <h2><?php echo $pending_payments; ?></h2>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <h3 class="section-title">Recent Bookings</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Passenger</th>
                            <th>Bus</th>
                            <th>Date</th>
                            <th>Seats</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($booking = $recent_bookings->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $booking['booking_id']; ?></td>
                            <td><?php echo $booking['passenger_name']; ?></td>
                            <td><?php echo $booking['bus_name']; ?></td>
                            <td><?php echo date('d M Y', strtotime($booking['departure_date'])); ?></td>
                            <td><?php echo $booking['seat_numbers']; ?></td>
                            <td>₹<?php echo number_format($booking['total_price'], 2); ?></td>
                            <td>
                                <span class="status <?php echo $booking['payment_status'] === 'paid' ? 'status-paid' : 'status-pending'; ?>">
                                    <?php echo ucfirst($booking['payment_status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_booking.php?id=<?php echo $booking['booking_id']; ?>" class="action-btn view-btn">View</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>