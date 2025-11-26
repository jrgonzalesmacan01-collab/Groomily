<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $owner_name = clean($_POST['owner_name']);
    $owner_phone = clean($_POST['owner_phone']);
    $dog_name = clean($_POST['dog_name']);
    $dog_age = intval($_POST['dog_age']);
    $dog_breed = clean($_POST['dog_breed']);
    $service_id = intval($_POST['service_id']);
    $booking_date = clean($_POST['booking_date']);
    $booking_time = clean($_POST['booking_time']);
    $payment_method = clean($_POST['payment_method']);
    
    // Payment details
    $card_number = isset($_POST['card_number']) ? clean($_POST['card_number']) : null;
    $card_holder = isset($_POST['card_holder']) ? clean($_POST['card_holder']) : null;
    $online_payment_id = isset($_POST['online_payment_id']) ? clean($_POST['online_payment_id']) : null;
    
    // Get service price
    $service_sql = "SELECT price FROM services WHERE service_id = $service_id";
    $service_result = $conn->query($service_sql);
    $service = $service_result->fetch_assoc();
    $total_amount = $service['price'];
    
    // Check slot availability
    $check_slot = "SELECT * FROM booking_slots 
                   WHERE booking_date = '$booking_date' 
                   AND booking_time = '$booking_time' 
                   AND current_bookings < max_bookings";
    $slot_result = $conn->query($check_slot);
    
    if ($slot_result->num_rows == 0) {
        $_SESSION['error'] = 'Selected time slot is no longer available!';
        redirect('booking.php');
    }
    
    // Generate booking code
    $booking_code = generateBookingCode();
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert booking
        $insert_booking = "INSERT INTO bookings 
                          (user_id, booking_code, owner_name, dog_name, dog_age, dog_breed, owner_phone, 
                           booking_date, booking_time, service_id, payment_method, card_number, card_holder, 
                           online_payment_id, total_amount, status, payment_status) 
                          VALUES 
                          ($user_id, '$booking_code', '$owner_name', '$dog_name', $dog_age, '$dog_breed', 
                           '$owner_phone', '$booking_date', '$booking_time', $service_id, '$payment_method', 
                           " . ($card_number ? "'$card_number'" : "NULL") . ", 
                           " . ($card_holder ? "'$card_holder'" : "NULL") . ", 
                           " . ($online_payment_id ? "'$online_payment_id'" : "NULL") . ", 
                           $total_amount, 'pending', 'pending')";
        
        if (!$conn->query($insert_booking)) {
            throw new Exception('Failed to create booking');
        }
        
        $booking_id = $conn->insert_id;
        
        // Update slot count
        $update_slot = "UPDATE booking_slots 
                       SET current_bookings = current_bookings + 1 
                       WHERE booking_date = '$booking_date' 
                       AND booking_time = '$booking_time'";
        
        if (!$conn->query($update_slot)) {
            throw new Exception('Failed to update slot');
        }
        
        // Commit transaction
        $conn->commit();
        
        // Store booking details in session for receipt page
        $_SESSION['last_booking'] = [
            'booking_id' => $booking_id,
            'booking_code' => $booking_code,
            'owner_name' => $owner_name,
            'owner_phone' => $owner_phone,
            'dog_name' => $dog_name,
            'dog_age' => $dog_age,
            'dog_breed' => $dog_breed,
            'booking_date' => $booking_date,
            'booking_time' => $booking_time,
            'service_id' => $service_id,
            'payment_method' => $payment_method,
            'total_amount' => $total_amount
        ];
        
        redirect('booking_receipt.php');
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = 'Booking failed: ' . $e->getMessage();
        redirect('booking.php');
    }
} else {
    redirect('booking.php');
}
?>