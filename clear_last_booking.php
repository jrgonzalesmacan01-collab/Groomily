<?php
require_once 'config.php';

// Clear last booking from session
if (isset($_SESSION['last_booking'])) {
    unset($_SESSION['last_booking']);
}

echo json_encode(['success' => true]);
?>