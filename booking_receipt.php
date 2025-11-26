<?php
require_once 'config.php';

if (!isLoggedIn() || !isset($_SESSION['last_booking'])) {
    redirect('user_dashboard.php');
}

$booking = $_SESSION['last_booking'];

// Get service details
$service_sql = "SELECT service_name FROM services WHERE service_id = " . $booking['service_id'];
$service_result = $conn->query($service_sql);
$service = $service_result->fetch_assoc();

// Simulate SMS sending (in real scenario, integrate with SMS API)
$sms_message = "Groomily Booking Confirmed!\nCode: {$booking['booking_code']}\nDog: {$booking['dog_name']}\nDate: " . date('M d, Y', strtotime($booking['booking_date'])) . "\nTime: " . date('g:i A', strtotime($booking['booking_time'])) . "\nAmount: ₱" . number_format($booking['total_amount'], 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Receipt - Groomily</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .receipt {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .success-icon {
            font-size: 5rem;
            text-align: center;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease-out;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .booking-code {
            background: linear-gradient(135deg, var(--primary-color), #ff8787);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 20px 0;
            letter-spacing: 2px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .detail-label {
            color: #666;
            font-weight: 600;
        }
        
        .detail-value {
            color: var(--dark-color);
            font-weight: bold;
        }
        
        .print-btn {
            margin-top: 20px;
        }
        
        @media print {
            body * {
                visibility: hidden;
            }
            .receipt, .receipt * {
                visibility: visible;
            }
            .receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .print-btn, .btn {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container" style="padding-top: 50px;">
        <div class="receipt">
            <div class="success-icon">✅</div>
            <h1 style="text-align: center; color: var(--primary-color);">Booking Confirmed!</h1>
            <p style="text-align: center; color: #666; margin-bottom: 30px;">Your dog grooming appointment has been successfully booked</p>
            
            <div class="booking-code">
                🎫 <?php echo $booking['booking_code']; ?>
            </div>
            
            <div style="margin: 30px 0;">
                <h3 style="margin-bottom: 15px;">📋 Booking Details</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Owner Name:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['owner_name']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Contact Number:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['owner_phone']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Dog Name:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['dog_name']); ?> 🐕</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Dog Age:</span>
                    <span class="detail-value"><?php echo $booking['dog_age']; ?> years old</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Breed:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['dog_breed']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Service:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($service['service_name']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value"><?php echo date('l, F d, Y', strtotime($booking['booking_date'])); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Time:</span>
                    <span class="detail-value"><?php echo date('g:i A', strtotime($booking['booking_time'])); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Payment Method:</span>
                    <span class="detail-value"><?php echo ucfirst($booking['payment_method']); ?></span>
                </div>
                
                <div class="detail-row" style="border-bottom: 3px solid var(--primary-color); padding: 20px 0;">
                    <span class="detail-label" style="font-size: 1.2rem;">Total Amount:</span>
                    <span class="detail-value" style="font-size: 1.5rem; color: var(--primary-color);">₱<?php echo number_format($booking['total_amount'], 2); ?></span>
                </div>
            </div>
            
            <div class="alert alert-info">
                <strong>📱 SMS Sent!</strong><br>
                A confirmation message has been sent to your phone number with your booking details.
            </div>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                <h4>📝 Important Notes:</h4>
                <ul style="margin: 10px 0; padding-left: 20px; color: #666;">
                    <li>Please arrive 10 minutes before your scheduled time</li>
                    <li>Bring your booking code for verification</li>
                    <li>Ensure your dog is on a leash or in a carrier</li>
                    <li>Payment will be processed at the facility</li>
                </ul>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 30px;">
                <button onclick="window.print()" class="btn btn-secondary btn-full">🖨️ Print Receipt</button>
                <a href="user_dashboard.php" class="btn btn-primary btn-full">Go to Dashboard</a>
            </div>
        </div>
    </div>
    
    <script>
        // Clear the session booking data after displaying
        setTimeout(() => {
            fetch('clear_last_booking.php');
        }, 2000);
    </script>
</body>
</html>