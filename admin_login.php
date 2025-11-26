<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

// Check if secret code was verified
if (!isset($_SESSION['secret_verified'])) {
    redirect('admin_secret.php');
}

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    redirect('admin_dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $captcha = $_POST['g-recaptcha-response'];
    
    // Verify CAPTCHA
    if (empty($captcha)) {
        $error = 'Please complete the CAPTCHA verification!';
    } elseif (!verifyCaptcha($captcha)) {
        $error = 'CAPTCHA verification failed. Please try again!';
    } elseif (empty($email) || empty($password)) {
        $error = 'Please enter both email and password!';
    } else {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
        
        if (!$stmt) {
            $error = 'Database error. Please try again.';
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $admin = $result->fetch_assoc();
                
                if ($password === $admin['password']) {
                    $_SESSION['admin_id'] = $admin['admin_id'];
                    $_SESSION['admin_name'] = $admin['admin_name'];
                    $_SESSION['admin_email'] = $admin['email'];
                    unset($_SESSION['secret_verified']);
                    
                    header('Location: admin_dashboard.php');
                    exit();
                } else {
                    $error = 'Invalid password!';
                }
            } else {
                $error = 'Admin account not found!';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Groomily</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            background: linear-gradient(135deg, #fc5c7d 0%, #6a82fb 100%);
        }
        
        .credentials-box {
            margin-top: 20px;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            color: white;
        }
        
        .credentials-box h4 {
            color: white;
            margin-bottom: 10px;
        }
        
        .credentials-box p {
            margin: 5px 0;
            font-size: 0.9rem;
        }
        
        .credentials-box strong {
            color: #FFE66D;
        }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="header">
        <div class="logo">🔧 Groomily Admin</div>
        <h2>Admin Login</h2>
        <p style="color: #666;">Access the management dashboard</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label>Admin Email</label>
            <input type="email" name="email" required placeholder="admin@groomily.com" 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Enter admin password">
        </div>
        
        <!-- reCAPTCHA -->
        <div class="form-group" style="display: flex; justify-content: center; margin: 20px 0;">
            <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-full">
            Login as Admin
        </button>
    </form>
    <center>
 <div class="credentials-box">
        <h4>ADMIN CAN ACCESS ONLY</h4>
        
    </div>
    </center>
   
    
    <p class="text-center mt-20">
        <a href="index.php" class="text-link">← Back to Home</a>
    </p>
</div>
</body>
</html>