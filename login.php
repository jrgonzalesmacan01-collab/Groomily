<?php
require_once 'config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    redirect('user_dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = clean($_POST['email']);
    $password = $_POST['password'];
    $captcha = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
    
    // Verify CAPTCHA
    $captcha_result = verifyCaptcha($captcha);
    if (!$captcha_result['success']) {
        $error = $captcha_result['error'];
    } elseif (empty($email) || empty($password)) {
        $error = 'Please enter both email and password!';
    } else {
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['just_logged_in'] = true; // Flag to trigger history replacement
                
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Location: user_dashboard.php");
                exit();
            } else {
                $error = 'Invalid password!';
            }
        } else {
            $error = 'Email not found!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Groomily</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="auth-container">
        <div class="header">
            <div class="logo">🐕 Groomily</div>
            <h2>Welcome Back!</h2>
            <p style="color: #666;">Login to manage your bookings</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" autocomplete="off">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="your@email.com" autocomplete="off">
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter your password" autocomplete="off">
            </div>
            
            <!-- reCAPTCHA -->
            <div class="form-group" style="display: flex; justify-content: center; margin: 20px 0;">
                <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full">Login 🐾</button>
        </form>
        
        <p class="text-center mt-20">
            Don't have an account? <a href="register.php" class="text-link">Register here</a>
        </p>
        
        <p class="text-center mt-20">
            <a href="index.php" class="text-link">← Back to Home</a>
        </p>
    </div>

    <script>
        // Clear form when page is loaded from cache (back button)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || performance.navigation.type === 2) {
                // Page was loaded from cache (back/forward button)
                document.querySelector('form').reset();
                // Reset reCAPTCHA
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.reset();
                }
            }
        });

        // Additional cleanup on page load
        window.addEventListener('load', function() {
            // Clear form fields
            document.querySelector('input[name="email"]').value = '';
            document.querySelector('input[name="password"]').value = '';
        });

        // 🔒 BLOCK FORWARD BUTTON - Disable going forward to dashboard
        window.history.forward();
        
        window.onunload = function() { 
            null 
        };
    </script>
</body>
</html>