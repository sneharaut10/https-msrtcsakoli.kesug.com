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

// Validate and sanitize input parameters
if (!isset($_GET['busId']) || !isset($_GET['departureDate']) || !isset($_GET['source']) || !isset($_GET['destination'])) {
    die("Required parameters are missing");
}

$busId = $conn->real_escape_string($_GET['busId']);
$date = $conn->real_escape_string($_GET['departureDate']);
$source = $conn->real_escape_string($_GET['source']);
$destination = $conn->real_escape_string($_GET['destination']);

// Convert date format from d-m-Y to Y-m-d
$departure_date = DateTime::createFromFormat('d-m-Y', $date);
if (!$departure_date) {
    die("Invalid date format. Please use dd-mm-yyyy format.");
}
$departure_date = $departure_date->format('Y-m-d');

// Get bus details with error handling
$bus_sql = "SELECT * FROM buses WHERE id = '$busId'";
$bus_result = $conn->query($bus_sql);

if ($bus_result === false) {
    die("Error fetching bus details: " . $conn->error);
}

if ($bus_result->num_rows == 0) {
    die("Error: No bus found with ID $busId");
}

$bus = $bus_result->fetch_assoc();

// Get booked seats with error handling
$booked_seats_sql = "SELECT seat_numbers FROM bookings 
                    WHERE bus_id = '$busId' 
                    AND departure_date = '$departure_date'
                    AND source = '$source' 
                    AND destination = '$destination'";
$booked_seats_result = $conn->query($booked_seats_sql);

if ($booked_seats_result === false) {
    die("Error fetching booked seats: " . $conn->error);
}

$booked_seats = [];
while ($row = $booked_seats_result->fetch_assoc()) {
    $seats = explode(',', $row['seat_numbers']);
    $booked_seats = array_merge($booked_seats, $seats);
}

// Remove any empty values and trim whitespace
$booked_seats = array_filter(array_map('trim', $booked_seats));

// Close connection
$conn->close();

// Pass data to JavaScript
echo '<script>';
echo 'const busDetails = ' . json_encode($bus) . ';';
echo 'const bookedSeats = ' . json_encode($booked_seats) . ';';
echo 'const departureDate = "' . htmlspecialchars($date) . '";';
echo 'const sourceCity = "' . htmlspecialchars($source) . '";';
echo 'const destinationCity = "' . htmlspecialchars($destination) . '";';
echo '</script>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seat Selection</title>
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
      padding: 20px;
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
      font-size: 24px;
      color: #2575fc;
      margin-bottom: 15px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    h1 span {
      color: #6a11cb;
    }

    /* Color Legend Styles */
    .legend {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      background: #f5f5f5;
      padding: 5px 10px;
      border-radius: 5px;
    }

    .legend-color {
      width: 20px;
      height: 20px;
      border-radius: 4px;
      border: 1px solid #ddd;
    }

    .legend-color.available-male {
      background: #d4e6ff;
      border-color: #2575fc;
    }

    .legend-color.available-female {
      background: #ffd6e7;
      border-color: #ff6b9d;
    }

    .legend-color.selected-male {
      background: #2575fc;
      border-color: #2575fc;
    }

    .legend-color.selected-female {
      background: #ff6b9d;
      border-color: #ff6b9d;
    }

    .legend-color.occupied {
      background: #ff4d4d;
      border-color: #ff4d4d;
    }

    .bus-layout {
      display: flex;
      flex-direction: column;
      gap: 20px;
      margin-bottom: 20px;
    }
    
    .deck {
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 15px;
      background-color: #f9f9f9;
    }
    
    .deck-title {
      font-weight: bold;
      margin-bottom: 10px;
      color: #2575fc;
    }
    
    .seat-grid {
      display: grid;
      grid-template-columns: repeat(10, 1fr);
      gap: 8px;
    }

    .seat {
      background: #f9f9f9;
      border: 1px solid #ddd;
      border-radius: 6px;
      padding: 8px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 12px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Seat Color Styles */
    .seat.available-male {
      background: #d4e6ff;
      border-color: #2575fc;
    }
    
    .seat.available-female {
      background: #ffd6e7;
      border-color: #ff6b9d;
    }

    .seat.selected-male {
      background: #2575fc;
      color: white;
      border-color: #2575fc;
      transform: scale(0.95);
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .seat.selected-female {
      background: #ff6b9d;
      color: white;
      border-color: #ff6b9d;
      transform: scale(0.95);
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .seat.occupied {
      background: #ff4d4d;
      color: white;
      cursor: not-allowed;
      opacity: 0.7;
    }
    
    .driver-seat {
      grid-column: 1;
      background: #333;
      color: white;
      cursor: default;
    }
    
    .steering {
      font-size: 20px;
    }
    
    .bus-door {
      grid-column: 5 / span 2;
      background: #ccc;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      cursor: default;
    }

    /* Rest of your existing styles... */
    .passenger-info {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-bottom: 15px;
      text-align: left;
      padding: 15px;
      background-color: #f5f5f5;
      border-radius: 8px;
    }
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
      padding: 20px;
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
      font-size: 24px;
      color: #2575fc;
      margin-bottom: 15px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
  
    h1 span {
      color: #6a11cb;
    }
  
    .bus-layout {
      display: flex;
      flex-direction: column;
      gap: 20px;
      margin-bottom: 20px;
    }
    
    .deck {
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 15px;
      background-color: #f9f9f9;
    }
    
    .deck-title {
      font-weight: bold;
      margin-bottom: 10px;
      color: #2575fc;
    }
    
    .seat-grid {
      display: grid;
      grid-template-columns: repeat(10, 1fr);
      gap: 8px;
    }
  
    .seat {
      background: #f9f9f9;
      border: 1px solid #ddd;
      border-radius: 6px;
      padding: 8px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease-in-out;
      font-size: 12px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  
    .seat.selected-male {
      background: #2575fc;
      color: #fff;
      border-color: #2575fc;
    }
    
    .seat.selected-female {
      background: #ff6b9d;
      color: #fff;
      border-color: #ff6b9d;
    }
  
    .seat.occupied {
      background: #ff4d4d;
      color: white;
      cursor: not-allowed;
      pointer-events: none;
    }
    
    .seat.available-male {
      background: #d4e6ff;
      border-color: #2575fc;
    }
    
    .seat.available-female {
      background: #ffd6e7;
      border-color: #ff6b9d;
    }
    
    .driver-seat {
      grid-column: 1;
      background: #333;
      color: white;
      cursor: default;
    }
    
    .steering {
      font-size: 20px;
    }
    
    .bus-door {
      grid-column: 5 / span 2;
      background: #ccc;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      cursor: default;
    }

    .passenger-info {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-bottom: 15px;
      text-align: left;
      padding: 15px;
      background-color: #f5f5f5;
      border-radius: 8px;
    }
  
    .passenger-info label {
      font-size: 14px;
      color: #555;
      margin-bottom: 5px;
      display: block;
    }
    
    .passenger-info input,
    .passenger-info select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 14px;
      background: #f9f9f9;
      transition: all 0.3s ease-in-out;
    }
  
    .passenger-info input:focus,
    .passenger-info select:focus {
      border-color: #2575fc;
      outline: none;
      box-shadow: 0 0 6px rgba(37, 117, 252, 0.5);
    }
  
    .payment-details {
      text-align: left;
      margin-bottom: 15px;
      padding: 15px;
      background-color: #f5f5f5;
      border-radius: 8px;
    }
  
    .payment-details p {
      margin: 5px 0;
      color: #555;
      font-size: 14px;
    }
  
    .payment-details .price {
      font-size: 18px;
      font-weight: 600;
      color: #6a11cb;
    }
  
    .proceed-btn {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease-in-out;
      width: 100%;
    }
  
    .proceed-btn:hover {
      background: linear-gradient(135deg, #2575fc, #6a11cb);
      transform: scale(1.02);
    }
  
    .proceed-btn:disabled {
      background: #cccccc;
      cursor: not-allowed;
      transform: none;
    }
  
    .proceed-btn.processing {
      position: relative;
    }
  
    .proceed-btn.processing::after {
      content: "";
      position: absolute;
      width: 16px;
      height: 16px;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      margin: auto;
      border: 3px solid transparent;
      border-top-color: #ffffff;
      border-radius: 50%;
      animation: button-loading-spinner 1s ease infinite;
    }
  
    @keyframes button-loading-spinner {
      from { transform: rotate(0turn); }
      to { transform: rotate(1turn); }
    }
    
    .legend {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-bottom: 15px;
    }
    
    .legend-item {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 12px;
    }
    
    .legend-color {
      width: 15px;
      height: 15px;
      border-radius: 3px;
    }

    .gender-selection {
      margin-bottom: 20px;
      padding: 15px;
      background-color: #f5f5f5;
      border-radius: 8px;
      text-align: left;
    }

    .gender-selection label {
      font-size: 14px;
      color: #555;
      margin-bottom: 5px;
      display: block;
    }

    .gender-selection select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 14px;
      background: #f9f9f9;
      transition: all 0.3s ease-in-out;
    }

    .gender-selection select:focus {
      border-color: #2575fc;
      outline: none;
      box-shadow: 0 0 6px rgba(37, 117, 252, 0.5);
    }

    /* ... (keep all your other existing styles) ... */

  </style>
</head>
<body>
  <div class="container">
    <h1>Seat Selection for <span id="busName"></span></h1>
    
    <!-- Color Legend Box -->
    <div class="legend">
      <div class="legend-item">
        <div class="legend-color available-male"></div>
        <span>Available (Male)</span>
      </div>
      <div class="legend-item">
        <div class="legend-color available-female"></div>
        <span>Available (Female)</span>
      </div>
      <div class="legend-item">
        <div class="legend-color selected-male"></div>
        <span>Selected (Male)</span>
      </div>
      <div class="legend-item">
        <div class="legend-color selected-female"></div>
        <span>Selected (Female)</span>
      </div>
      <div class="legend-item">
        <div class="legend-color occupied"></div>
        <span>Booked</span>
      </div>
    </div>

    <!-- Gender Selection Section -->
    <div class="gender-selection">
      <h3>Select Your Gender First</h3>
      <label for="gender">Gender*</label>
      <select id="gender" required>
        <option value="" disabled selected>Select Gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
      </select>
    </div>
    
    <div class="bus-layout">
      <div class="deck">
        <div class="deck-title">Upper Deck</div>
        <div class="seat-grid" id="upperDeck">
          <!-- Upper deck seats will be inserted here -->
        </div>
      </div>
      
      <div class="deck">
        <div class="deck-title">Lower Deck</div>
        <div class="seat-grid" id="lowerDeck">
          <!-- Lower deck seats will be inserted here -->
          <div class="driver-seat"><span class="steering">🚌</span></div>
          <div class="seat available-male" data-seat-number="21" data-deck="lower">21</div>
          <div class="seat available-male" data-seat-number="22" data-deck="lower">22</div>
          <div class="seat available-male" data-seat-number="23" data-deck="lower">23</div>
          <div class="bus-door">Door</div>
          <div class="seat available-female" data-seat-number="24" data-deck="lower">24</div>
          <div class="seat available-female" data-seat-number="25" data-deck="lower">25</div>
          <div class="seat available-female" data-seat-number="26" data-deck="lower">26</div>
          <div class="seat available-female" data-seat-number="27" data-deck="lower">27</div>
          <div class="seat available-female" data-seat-number="28" data-deck="lower">28</div>
        </div>
      </div>
    </div>

    <!-- Passenger Information Section -->
    <div class="passenger-info">
      <h3>Passenger Information</h3>
      <div>
        <label for="name">Full Name*</label>
        <input type="text" id="name" placeholder="Enter passenger name" required>
      </div>
      <div>
        <label for="age">Age*</label>
        <input type="number" id="age" placeholder="Enter age" required min="1" max="120">
      </div>
      <div>
        <label for="mobile">Mobile Number*</label>
        <input type="tel" id="mobile" placeholder="Enter 10-digit mobile number" required pattern="[0-9]{10}">
      </div>
      <div>
        <label for="alternateMobile">Alternate Mobile Number (Optional)</label>
        <input type="tel" id="alternateMobile" placeholder="Enter alternate mobile number" pattern="[0-9]{10}">
      </div>
    </div>

    <!-- Payment Details Section -->
    <div class="payment-details">
      <h3>Booking Summary</h3>
      <p><strong>Selected Bus:</strong> <span id="selectedBusName"></span></p>
      <p><strong>Departure Date:</strong> <span id="departureDate"></span></p>
      <p><strong>From:</strong> <span id="sourceCity"></span></p>
      <p><strong>To:</strong> <span id="destinationCity"></span></p>
      <p><strong>Selected Seats:</strong> <span id="selectedSeatsList">None</span></p>
      <p><strong>Total Price:</strong> <span class="price" id="totalPrice">₹0</span></p>
    </div>

    <!-- Proceed to Payment Button -->
    <button class="proceed-btn" id="proceedBtn">Proceed to Payment</button>
  </div>

  <script>
    // Get data from PHP
    const busId = busDetails.id;
    const busName = busDetails.name;
    const malePrice = parseFloat(busDetails.male_price);
    const femalePrice = parseFloat(busDetails.female_price);
    
    let selectedSeats = [];

    // Display bus info
    document.getElementById('busName').textContent = busName;
    document.getElementById('selectedBusName').textContent = busName;
    document.getElementById('departureDate').textContent = departureDate;
    document.getElementById('sourceCity').textContent = sourceCity;
    document.getElementById('destinationCity').textContent = destinationCity;

    // Generate seats
    function generateSeats() {
      const upperDeck = document.getElementById('upperDeck');
      upperDeck.innerHTML = '';
      
      const lowerDeck = document.getElementById('lowerDeck');
      
      // Generate upper deck seats (1-20)
      for (let i = 1; i <= 20; i++) {
        const seatElement = document.createElement('div');
        seatElement.className = i <= 10 ? 'seat available-male' : 'seat available-female';
        seatElement.textContent = i;
        seatElement.dataset.seatNumber = i;
        seatElement.dataset.deck = 'upper';
        seatElement.addEventListener('click', () => toggleSeatSelection(seatElement, i));
        upperDeck.appendChild(seatElement);
      }
      
      // Generate remaining lower deck seats (29-40)
      for (let i = 29; i <= 40; i++) {
        const seatElement = document.createElement('div');
        seatElement.className = i <= 34 ? 'seat available-male' : 'seat available-female';
        seatElement.textContent = i;
        seatElement.dataset.seatNumber = i;
        seatElement.dataset.deck = 'lower';
        seatElement.addEventListener('click', () => toggleSeatSelection(seatElement, i));
        lowerDeck.appendChild(seatElement);
      }
      
      // Mark booked seats as occupied
      bookedSeats.forEach(seatNumber => {
        const seatElement = document.querySelector(`[data-seat-number="${seatNumber}"]`);
        if (seatElement) {
          seatElement.classList.remove('available-male', 'available-female');
          seatElement.classList.add('occupied');
        }
      });
    }

    // Toggle seat selection
    function toggleSeatSelection(seatElement, seatNumber) {
      if (seatElement.classList.contains('occupied')) return;
      
      const gender = document.getElementById('gender').value;
      if (!gender) {
        alert('Please select your gender first from the Gender Selection section above');
        return;
      }
      
      // Check if seat is already selected
      const isSelected = selectedSeats.some(seat => seat.number === seatNumber);
      
      if (isSelected) {
        // Deselect the seat
        seatElement.classList.remove('selected-male', 'selected-female');
        seatElement.classList.add(gender === 'female' ? 'available-female' : 'available-male');
        selectedSeats = selectedSeats.filter(seat => seat.number !== seatNumber);
      } else {
        // Select the seat
        const selectedClass = gender === 'female' ? 'selected-female' : 'selected-male';
        seatElement.classList.remove('available-male', 'available-female');
        seatElement.classList.add(selectedClass);
        selectedSeats.push({
          number: seatNumber,
          deck: seatElement.dataset.deck,
          gender: gender
        });
      }
      
      updateSelectedSeatsDisplay();
      updatePrice();
    }
    
    // Update selected seats display
    function updateSelectedSeatsDisplay() {
      const selectedSeatsList = document.getElementById('selectedSeatsList');
      if (selectedSeats.length === 0) {
        selectedSeatsList.textContent = 'None';
        return;
      }
      
      selectedSeatsList.textContent = selectedSeats.map(seat => 
        `${seat.number} (${seat.gender.charAt(0).toUpperCase() + seat.gender.slice(1)})`
      ).join(', ');
    }

    // Update price display
    function updatePrice() {
      if (selectedSeats.length === 0) {
        document.getElementById('totalPrice').textContent = '₹0';
        return;
      }
      
      let totalPrice = 0;
      selectedSeats.forEach(seat => {
        totalPrice += seat.gender === 'female' ? femalePrice : malePrice;
      });
      
      document.getElementById('totalPrice').textContent = `₹${totalPrice.toFixed(2)}`;
    }

    function proceedToPayment() {
      const name = document.getElementById('name').value.trim();
      const age = document.getElementById('age').value.trim();
      const mobile = document.getElementById('mobile').value.trim();
      const gender = document.getElementById('gender').value;
      const totalPrice = document.getElementById('totalPrice').textContent.replace('₹', '');
      const seatNumbers = selectedSeats.map(seat => seat.number).join(',');
      const alternateMobile = document.getElementById('alternateMobile').value.trim() || '';

      // Validate inputs
      if (!name || !age || !mobile || !gender || selectedSeats.length === 0) {
          alert('Please fill all required fields and select at least one seat');
          return;
      }
      
      if (!/^\d{10}$/.test(mobile)) {
          alert('Please enter a valid 10-digit mobile number');
          return;
      }

      if (alternateMobile && !/^\d{10}$/.test(alternateMobile)) {
          alert('Please enter a valid 10-digit alternate mobile number');
          return;
      }

      // Disable button to prevent double submission
      const btn = document.getElementById('proceedBtn');
      btn.disabled = true;
      btn.textContent = 'Processing...';

      // Create a form dynamically
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'process_booking.php';
      form.style.display = 'none';

      // Add all required fields to the form
      function addField(name, value) {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = name;
          input.value = value;
          form.appendChild(input);
      }

      addField('bus_id', busId);
      addField('bus_name', busName);
      addField('departure_date', departureDate);
      addField('source', sourceCity);
      addField('destination', destinationCity);
      addField('passenger_name', name);
      addField('passenger_age', age);
      addField('passenger_gender', gender);
      addField('passenger_mobile', mobile);
      addField('alternate_mobile', alternateMobile);
      addField('seat_numbers', seatNumbers);
      addField('total_price', totalPrice);

      // Add form to body and submit
      document.body.appendChild(form);
      form.submit();
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', () => {
      generateSeats();
      
      // Set up event listener for proceed button
      document.getElementById('proceedBtn').addEventListener('click', proceedToPayment);
      
      // Update price when gender changes
      document.getElementById('gender').addEventListener('change', updatePrice);
    });
  </script>
</body>
</html>