<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Get services
$services_sql = "SELECT * FROM services WHERE status = 'active' ORDER BY price ASC";
$services_result = $conn->query($services_sql);

// Get available dates and times
$available_slots_sql = "SELECT DISTINCT booking_date, booking_time 
                        FROM booking_slots 
                        WHERE booking_date >= CURDATE() 
                        AND current_bookings < max_bookings 
                        ORDER BY booking_date, booking_time";
$slots_result = $conn->query($available_slots_sql);

$available_dates = [];
$available_times = [];

while ($slot = $slots_result->fetch_assoc()) {
    $date = $slot['booking_date'];
    $time = $slot['booking_time'];
    
    if (!isset($available_dates[$date])) {
        $available_dates[$date] = [];
    }
    $available_dates[$date][] = $time;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking - Groomily</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .service-item {
            border: 3px solid #e0e0e0;
            padding: 20px;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        
        .service-item:hover {
            border-color: var(--secondary-color);
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .service-item.selected {
            border-color: var(--primary-color);
            background: #fff5f5;
        }
        
        .service-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .time-slot {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .time-slot:hover {
            border-color: var(--secondary-color);
            background: #f0f9f9;
        }
        
        .time-slot.selected {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
        }
        
        .time-slot.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Calendar Styles */
        .calendar-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-header button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .calendar-header h3 {
            margin: 0;
            color: var(--dark-color);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: bold;
            color: var(--dark-color);
            padding: 10px;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e0e0e0;
            background: white;
        }

        .calendar-day:hover:not(.disabled):not(.empty) {
            border-color: var(--secondary-color);
            background: #f0f9f9;
        }

        .calendar-day.selected {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .calendar-day.disabled {
            opacity: 0.3;
            cursor: not-allowed;
            background: #f5f5f5;
        }

        .calendar-day.empty {
            border: none;
            cursor: default;
        }

        .calendar-day.available {
            border-color: var(--secondary-color);
            background: #f0f9f9;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-content">
            <div class="logo" style="font-size: 1.8rem;">Groomily</div>
            <ul class="nav-links">
                <li><a href="user_dashboard.php">Dashboard 🏠</a></li>
                <li><a href="booking.php">New Booking 📅</a></li>
                <li><a href="logout.php">Logout 👋</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <div class="card">
            <h1>📅 Book Your Dog's Grooming Session</h1>
            <p style="color: #666;">Fill in the details below to schedule an appointment</p>
        </div>
        
        <form method="POST" action="process_booking.php" id="bookingForm">
            <!-- Dog Owner Information -->
            <div class="card">
                <h2>👤 Owner Information</h2>
                <div class="form-group">
                    <label>Owner Name</label>
                    <input type="text" name="owner_name" required value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>">
                </div>
                
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="tel" name="owner_phone" required placeholder="+63 XXX XXX XXXX">
                </div>
            </div>
            
            <!-- Dog Information -->
            <div class="card">
                <h2>🐕 Dog Information</h2>
                <div class="form-group">
                    <label>Dog Name</label>
                    <input type="text" name="dog_name" required placeholder="Enter your dog's name">
                </div>
                
                <div class="form-group">
                    <label>Dog Age (years)</label>
                    <input type="number" name="dog_age" min="0" max="30" required placeholder="Enter dog's age">
                </div>
                
                <div class="form-group">
                    <label>Dog Breed</label>
                    <input type="text" name="dog_breed" required placeholder="e.g., Labrador, Shih Tzu, etc.">
                </div>
            </div>
            
            <!-- Service Selection -->
            <div class="card">
                <h2>✨ Select Service</h2>
                <div class="service-grid">
                    <?php 
                    $icons = ['🛁', '💅', '🌟', '🎀', '🦷', '🐾', '✂️', '🐶'];
                    $i = 0;
                    while ($service = $services_result->fetch_assoc()): 
                    ?>
                        <div class="service-item" onclick="selectService(<?php echo $service['service_id']; ?>, <?php echo $service['price']; ?>)">
                            <input type="radio" name="service_id" value="<?php echo $service['service_id']; ?>" id="service_<?php echo $service['service_id']; ?>" style="display: none;" required>
                            <div class="service-icon"><?php echo $icons[$i % count($icons)]; ?></div>
                            <h3><?php echo htmlspecialchars($service['service_name']); ?></h3>
                            <p style="color: #666; font-size: 0.9rem; margin: 10px 0;"><?php echo htmlspecialchars($service['description']); ?></p>
                            <p style="font-weight: bold; color: var(--primary-color); font-size: 1.2rem;">₱<?php echo number_format($service['price'], 2); ?></p>
                            <p style="color: #999; font-size: 0.85rem;">⏱️ <?php echo $service['duration_minutes']; ?> minutes</p>
                        </div>
                    <?php 
                    $i++;
                    endwhile; 
                    ?>
                </div>
            </div>
            
            <!-- Date and Time Selection with Calendar -->
            <div class="card">
                <h2>📆 Select Date & Time</h2>
                
                <div class="calendar-container">
                    <div class="calendar-header">
                        <button type="button" onclick="previousMonth()">◀</button>
                        <h3 id="currentMonth"></h3>
                        <button type="button" onclick="nextMonth()">▶</button>
                    </div>
                    
                    <div class="calendar-grid" id="calendarGrid"></div>
                </div>
                
                <input type="hidden" name="booking_date" id="selected_date" required>
                
                <div class="form-group" style="margin-top: 20px;">
                    <label>Select Time</label>
                    <div id="time-slots-container" style="margin-top: 15px;">
                        <p style="color: #999;">Please select a date first</p>
                    </div>
                    <input type="hidden" name="booking_time" id="selected_time" required>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="card">
                <h2>💳 Payment Method</h2>
                <div class="form-group">
                    <label>Choose Payment Method</label>
                    <select name="payment_method" id="payment_method" required onchange="togglePaymentFields()">
                        <option value="">Select payment method</option>
                        <option value="cash">Cash 💵</option>
                        <option value="card">Credit/Debit Card 💳</option>
                        <option value="online">Online Payment 📱</option>
                    </select>
                </div>
                
                <div id="card_fields" style="display: none;">
                    <div class="form-group">
                        <label>Card Number</label>
                        <input type="text" name="card_number" placeholder="XXXX XXXX XXXX XXXX" maxlength="19">
                    </div>
                    <div class="form-group">
                        <label>Card Holder Name</label>
                        <input type="text" name="card_holder" placeholder="Name on card">
                    </div>
                </div>
                
                <div id="online_fields" style="display: none;">
                    <div class="form-group">
                        <label>Online Payment Reference/Transaction ID</label>
                        <input type="text" name="online_payment_id" placeholder="Enter transaction ID">
                    </div>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h2 style="color: white;">💰 Total Amount: ₱<span id="total_amount">0.00</span></h2>
                <button type="submit" class="btn btn-secondary btn-full" style="margin-top: 20px;">Confirm Booking 🐾</button>
            </div>
        </form>
    </div>
    
    <script>
        const availableDates = <?php echo json_encode($available_dates); ?>;
        let currentDate = new Date();
        let selectedDate = null;
        
        // Initialize calendar
        renderCalendar();
        
        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            // Update header
            const monthNames = ["January", "February", "March", "April", "May", "June",
                              "July", "August", "September", "October", "November", "December"];
            document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
            
            // Get first day of month and number of days
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            
            // Build calendar
            let html = '';
            
            // Day headers
            const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            dayHeaders.forEach(day => {
                html += `<div class="calendar-day-header">${day}</div>`;
            });
            
            // Empty cells before first day
            for (let i = 0; i < firstDay; i++) {
                html += '<div class="calendar-day empty"></div>';
            }
            
            // Days of month
            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const isAvailable = availableDates.hasOwnProperty(dateStr);
                const isPast = new Date(dateStr) < new Date().setHours(0, 0, 0, 0);
                const isSelected = selectedDate === dateStr;
                
                let classes = 'calendar-day';
                if (isPast) classes += ' disabled';
                else if (isAvailable) classes += ' available';
                if (isSelected) classes += ' selected';
                
                html += `<div class="${classes}" onclick="selectDate('${dateStr}')" data-date="${dateStr}">${day}</div>`;
            }
            
            document.getElementById('calendarGrid').innerHTML = html;
        }
        
        function selectDate(dateStr) {
            if (!availableDates.hasOwnProperty(dateStr)) {
                alert('No available time slots for this date. Please choose another date.');
                return;
            }
            
            selectedDate = dateStr;
            document.getElementById('selected_date').value = dateStr;
            
            // Update calendar display
            document.querySelectorAll('.calendar-day').forEach(day => {
                day.classList.remove('selected');
                if (day.dataset.date === dateStr) {
                    day.classList.add('selected');
                }
            });
            
            // Update time slots
            updateTimeSlots(dateStr);
        }
        
        function updateTimeSlots(dateStr) {
            const container = document.getElementById('time-slots-container');
            
            if (!dateStr || !availableDates[dateStr]) {
                container.innerHTML = '<p style="color: #999;">Please select a date first</p>';
                return;
            }
            
            const times = availableDates[dateStr];
            let html = '';
            
            times.forEach(time => {
                const displayTime = formatTime(time);
                html += `<span class="time-slot" onclick="selectTime('${time}')">${displayTime}</span>`;
            });
            
            container.innerHTML = html;
        }
        
        function previousMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        }
        
        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        }
        
        function selectService(serviceId, price) {
            document.querySelectorAll('.service-item').forEach(item => {
                item.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            document.getElementById('service_' + serviceId).checked = true;
            document.getElementById('total_amount').textContent = price.toFixed(2);
        }
        
        function selectTime(time) {
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            document.getElementById('selected_time').value = time;
        }
        
        function formatTime(time) {
            const [hours, minutes] = time.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 || 12;
            return `${displayHour}:${minutes} ${ampm}`;
        }
        
        function togglePaymentFields() {
            const method = document.getElementById('payment_method').value;
            document.getElementById('card_fields').style.display = method === 'card' ? 'block' : 'none';
            document.getElementById('online_fields').style.display = method === 'online' ? 'block' : 'none';
            
            // Update required fields
            const cardInputs = document.querySelectorAll('#card_fields input');
            const onlineInputs = document.querySelectorAll('#online_fields input');
            
            cardInputs.forEach(input => input.required = method === 'card');
            onlineInputs.forEach(input => input.required = method === 'online');
        }
        
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            if (!document.getElementById('selected_time').value) {
                e.preventDefault();
                alert('Please select a time slot!');
                return false;
            }
            if (!document.getElementById('selected_date').value) {
                e.preventDefault();
                alert('Please select a date!');
                return false;
            }
        });
    </script>
</body>
</html>