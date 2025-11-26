<?php
require_once 'config.php';

if (!isAdminLoggedIn()) {
    redirect('admin_secret.php');
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $booking_id = intval($_GET['id']);
    $status = clean($_GET['status']);
    $return = isset($_GET['return']) ? clean($_GET['return']) : 'dashboard';
    
    $allowed_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    
    if (in_array($status, $allowed_statuses)) {
        $update_sql = "UPDATE bookings SET status = '$status' WHERE booking_id = $booking_id";
        
        if ($conn->query($update_sql)) {
            $_SESSION['success'] = 'Booking status updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update booking status.';
        }
    }
    
    if ($return == 'bookings') {
        redirect('admin_bookings.php');
    } else {
        redirect('admin_dashboard.php');
    }
} else {
    redirect('admin_dashboard.php');
}
?>