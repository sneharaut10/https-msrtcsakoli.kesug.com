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

// Get form data and sanitize
$source = $conn->real_escape_string($_GET['source']);
$destination = $conn->real_escape_string($_GET['destination']);
$departure = $conn->real_escape_string($_GET['departure']);

// Convert date format from d-m-Y (form input) to Y-m-d (MySQL)
$departure_date = DateTime::createFromFormat('d-m-Y', $departure)->format('Y-m-d');

// Query to get available buses
$sql = "SELECT * FROM buses 
        WHERE source='$source' AND destination='$destination'
        ORDER BY departure_time ASC";
$result = $conn->query($sql);

// Store search history (optional)
$insert_sql = "INSERT INTO routes (source, destination, departure_date) 
               VALUES ('$source', '$destination', '$departure_date')";
$conn->query($insert_sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Buses</title>
    <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: #333;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      animation: gradientAnimation 10s ease infinite;
    }

    @keyframes gradientAnimation {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .container {
      max-width: 800px;
      width: 100%;
      padding: 30px;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      text-align: center;
      animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h1 {
      font-size: 28px;
      color: #2575fc;
      margin-bottom: 20px;
      text-transform: uppercase;
      letter-spacing: 1.5px;
    }

    h1 span {
      color: #6a11cb;
    }

    .search-info {
      background: #f0f0f0;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: left;
    }

    .bus-list {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .bus-card {
      background: #f9f9f9;
      border: 2px solid #ddd;
      border-radius: 8px;
      padding: 20px;
      text-align: left;
      transition: all 0.3s ease-in-out;
    }

    .bus-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .bus-card h3 {
      margin: 0;
      font-size: 20px;
      color: #2575fc;
    }

    .bus-details {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin: 15px 0;
    }

    .bus-details p {
      margin: 5px 0;
      color: #555;
    }

    .price-section {
      background: #f0f7ff;
      padding: 10px;
      border-radius: 8px;
      margin: 10px 0;
    }

    .price {
      font-size: 18px;
      font-weight: 600;
      color: #6a11cb;
    }

    .view-seats-btn {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 10px 20px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease-in-out;
      width: 100%;
    }

    .view-seats-btn:hover {
      background: linear-gradient(135deg, #2575fc, #6a11cb);
      transform: scale(1.02);
    }
    
    .no-buses {
      padding: 20px;
      background: #fff8f8;
      border: 1px solid #ffdddd;
      border-radius: 8px;
      color: #d33;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Available <span>Buses</span></h1>
    
    <div class="search-info">
      <h3>Your Journey Details:</h3>
      <p><strong>From:</strong> <?= htmlspecialchars($source) ?></p>
      <p><strong>To:</strong> <?= htmlspecialchars($destination) ?></p>
      <p><strong>Date:</strong> <?= htmlspecialchars($departure) ?></p>
    </div>

    <div class="bus-list">
      <?php if ($result->num_rows > 0): ?>
        <?php while($bus = $result->fetch_assoc()): ?>
          <div class="bus-card">
            <h3><?= htmlspecialchars($bus['name']) ?></h3>
            
            <div class="bus-details">
              <p><strong>Departure Time:</strong> <?= htmlspecialchars($bus['departure_time']) ?></p>
              <p><strong>Schedule:</strong> <?= htmlspecialchars($bus['schedule']) ?></p>
            </div>
            
            <div class="price-section">
              <p class="price">Male Price: ₹<?= htmlspecialchars($bus['male_price']) ?></p>
              <p class="price">Female Price: ₹<?= htmlspecialchars($bus['female_price']) ?></p>
            </div>
            
            <button class="view-seats-btn" 
              onclick="window.location.href='seat_selection.php?busId=<?= $bus['id'] ?>&busName=<?= urlencode($bus['name']) ?>&malePrice=<?= $bus['male_price'] ?>&femalePrice=<?= $bus['female_price'] ?>&departureDate=<?= urlencode($departure) ?>&source=<?= urlencode($source) ?>&destination=<?= urlencode($destination) ?>'">
              View Seats & Book
            </button>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="no-buses">
          <p>No buses available for this route on the selected date.</p>
          <p>Please try different source, destination, or date.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
<?php
$conn->close();
?>