<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['settings'] = [
        'site_name' => $_POST['site_name'],
        'currency' => $_POST['currency'],
        'timezone' => $_POST['timezone'],
        'booking_expiry' => $_POST['booking_expiry'],
        'admin_email' => $_POST['admin_email'],
        'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0
    ];
    $success_message = "Settings saved successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
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
                <div class="menu-item">
                    <i class="fas fa-money-bill-wave"></i>
                    <a href="manage_payments.php">Manage Payments</a>
                </div>
               
                <div class="menu-item active">
                    <i class="fas fa-cog"></i>
                    <a href="settings.php">Settings</a>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="header">
                <h2>Settings</h2>
                <div class="user-info">
                    <img src="admin.png" alt="Admin">
                    <span><?php echo $_SESSION['admin_username']; ?></span>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            
            <?php if (isset($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <div class="card">
                <form method="POST">
                    <div class="form-group">
                        <h3 class="section-title">General Settings</h3>
                        
                        <div class="form-group">
                            <label for="site_name">Site Name</label>
                            <input type="text" id="site_name" name="site_name" 
                                   value="<?php echo isset($_SESSION['settings']['site_name']) ? $_SESSION['settings']['site_name'] : 'Bus Booking System'; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select id="currency" name="currency">
                                <option value="₹" <?php echo (isset($_SESSION['settings']['currency']) && $_SESSION['settings']['currency'] === '₹' ? 'selected' : ''); ?>>Indian Rupee (₹)</option>
                                <option value="$" <?php echo (isset($_SESSION['settings']['currency']) && $_SESSION['settings']['currency'] === '$' ? 'selected' : ''); ?>>US Dollar ($)</option>
                                <option value="€" <?php echo (isset($_SESSION['settings']['currency']) && $_SESSION['settings']['currency'] === '€' ? 'selected' : ''); ?>>Euro (€)</option>
                                <option value="£" <?php echo (isset($_SESSION['settings']['currency']) && $_SESSION['settings']['currency'] === '£' ? 'selected' : ''); ?>>Pound Sterling (£)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="timezone">Timezone</label>
                            <select id="timezone" name="timezone">
                                <option value="Asia/Kolkata" <?php echo (isset($_SESSION['settings']['timezone']) && $_SESSION['settings']['timezone'] === 'Asia/Kolkata' ? 'selected' : ''); ?>>(GMT+5:30) India</option>
                                <option value="America/New_York" <?php echo (isset($_SESSION['settings']['timezone']) && $_SESSION['settings']['timezone'] === 'America/New_York' ? 'selected' : ''); ?>>(GMT-5:00) Eastern Time (US & Canada)</option>
                                <option value="Europe/London" <?php echo (isset($_SESSION['settings']['timezone']) && $_SESSION['settings']['timezone'] === 'Europe/London' ? 'selected' : ''); ?>>(GMT+0:00) London</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <h3 class="section-title">Booking Settings</h3>
                        
                        <div class="form-group">
                            <label for="booking_expiry">Booking Expiry Time (minutes)</label>
                            <input type="number" id="booking_expiry" name="booking_expiry" min="1" 
                                   value="<?php echo isset($_SESSION['settings']['booking_expiry']) ? $_SESSION['settings']['booking_expiry'] : '30'; ?>" required>
                            <small>Time before an unpaid booking is automatically cancelled</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <h3 class="section-title">Administration</h3>
                        
                        <div class="form-group">
                            <label for="admin_email">Admin Email</label>
                            <input type="email" id="admin_email" name="admin_email" 
                                   value="<?php echo isset($_SESSION['settings']['admin_email']) ? $_SESSION['settings']['admin_email'] : 'admin@busbookingsystem.com'; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                                       <?php echo (isset($_SESSION['settings']['maintenance_mode'])) && $_SESSION['settings']['maintenance_mode'] == 1 ? 'checked' : ''; ?>>
                                Enable Maintenance Mode
                            </label>
                            <small>When enabled, only administrators can access the site</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>