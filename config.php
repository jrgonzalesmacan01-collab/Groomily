<?php

// Database Configuration
// reCAPTCHA keys
define('RECAPTCHA_SITE_KEY', '6Lci2RgsAAAAAKtaY5EXbUqpWiNrGQUk_r6s-jyJ');
define('RECAPTCHA_SECRET_KEY', '6Lci2RgsAAAAAE7YlH5nPfVmO1agUHOMZOI24idA');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'groomily');

// 🔒 Disable caching for ALL pages that include config.php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");



// Verify reCAPTCHA with detailed error reporting
function verifyCaptcha($response) {
    if (empty($response)) {
        return ['success' => false, 'error' => 'No reCAPTCHA response received.'];
    }

    $secret = RECAPTCHA_SECRET_KEY;
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
    
    if ($verify === false) {
        return ['success' => false, 'error' => 'Unable to contact reCAPTCHA server.'];
    }

    $captcha_success = json_decode($verify, true);

    if (isset($captcha_success['success']) && $captcha_success['success'] === true) {
        return ['success' => true];
    } else {
        $error_codes = isset($captcha_success['error-codes']) ? implode(', ', $captcha_success['error-codes']) : 'Unknown error';
        return ['success' => false, 'error' => 'CAPTCHA verification failed: ' . $error_codes];
    }
}


// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to generate booking code
function generateBookingCode() {
    return 'GROOM-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Function to sanitize input
function clean($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}
?>
