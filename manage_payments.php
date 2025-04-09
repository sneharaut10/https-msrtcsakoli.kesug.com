<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "bus_booking");

if (isset($_POST['update_payment'])) {
    $booking_id = $conn->real_escape_string($_POST['booking_id']);
    $status = $conn->real_escape_string($_POST['status']);
    $transaction_id = isset($_POST['transaction_id']) ? $conn->real_escape_string($_POST['transaction_id']) : null;
    
    $conn->query("UPDATE bookings SET payment_status = '$status', transaction_id = '$transaction_id' WHERE booking_id = '$booking_id'");
    
    if ($status === 'paid' && $transaction_id) {
        $amount = $conn->query("SELECT total_price FROM bookings WHERE booking_id = '$booking_id'")->fetch_assoc()['total_price'];
        $conn->query("INSERT INTO payments (booking_id, transaction_id, amount) VALUES ('$booking_id', '$transaction_id', '$amount')");
    }
}

$payments = $conn->query("SELECT b.*, p.payment_date 
                         FROM bookings b
                         LEFT JOIN payments p ON b.booking_id = p.booking_id
                         ORDER BY b.booking_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="admin_styles.css">
</head>
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

</style>
<body>
    <div class="dashboard">
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
                <div class="menu-item active">
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
                <h2>Manage Payments</h2>
                <div class="user-info">
                    <img src="admin.png" alt="Admin">
                    <span><?php echo $_SESSION['admin_username']; ?></span>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            
            <div class="card">
                <h3 class="section-title">Payment Management</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Passenger</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Transaction ID</th>
                            <th>Payment Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($payment = $payments->fetch_assoc()): ?>
                        <tr>
                            <td><?= $payment['booking_id'] ?></td>
                            <td><?= $payment['passenger_name'] ?></td>
                            <td>₹<?= number_format($payment['total_price'], 2) ?></td>
                            <td>
                                <form method="POST" class="status-form">
                                    <select name="status" class="status-select <?= $payment['payment_status'] ?>">
                                        <option value="paid" <?= $payment['payment_status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                                        <option value="pending" <?= $payment['payment_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    </select>
                                    <div class="payment-form <?= $payment['payment_status'] == 'paid' ? '' : 'show-form' ?>">
                                        <input type="text" name="transaction_id" placeholder="Transaction ID" value="<?= $payment['transaction_id'] ?? '' ?>">
                                        <input type="hidden" name="booking_id" value="<?= $payment['booking_id'] ?>">
                                        <button type="submit" name="update_payment" class="btn">Update</button>
                                    </div>
                                </form>
                            </td>
                            <td><?= $payment['transaction_id'] ?? 'N/A' ?></td>
                            <td><?= $payment['payment_date'] ?? 'N/A' ?></td>
                            <td>
                                <a href="payment_receipt.php?id=<?= $payment['booking_id'] ?>" class="action-btn view-btn">Receipt</a>
                                <a href="#" class="action-btn reject-btn">Refund</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function() {
                const form = this.closest('.status-form').querySelector('.payment-form');
                if (this.value === 'paid') {
                    form.classList.add('show-form');
                } else {
                    form.classList.remove('show-form');
                }
            });
        });
    </script>
</body>
</html>