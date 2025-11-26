<?php
require_once 'config.php';

$error = '';
$show_hint = true; // Set to false in production

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $secret_code = clean($_POST['secret_code']);
    
    if ($secret_code == 'WOOF2024') {
        $_SESSION['secret_verified'] = true;
        redirect('admin_login.php');
    } else {
        $error = 'Invalid secret code! Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access - Groomily</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .secret-container {
            max-width: 450px;
            margin: 100px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            text-align: center;
        }
        
        .lock-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .hint-box {
            margin-top: 20px;
            padding: 15px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border-radius: 10px;
            color: white;
        }
        
        .hint-box strong {
            color: #FFE66D;
            font-size: 1.2rem;
        }
        
        .secret-input {
            text-align: center;
            font-size: 1.3rem;
            letter-spacing: 3px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="secret-container">
        <div class="lock-icon">🔐</div>
        <h2>Admin Access Portal</h2>
        <p style="color: #666; margin-bottom: 30px;">Enter the code to continue</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <input type="password" 
                       name="secret_code" 
                       required 
                       placeholder="Enter Secret Code" 
                       class="secret-input"
                       autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary btn-full">
                Verify Code
            </button>
        </form>
        
        <?php if ($show_hint): ?>
        <div class="hint-box">
            <p style="margin: 0; font-size: 0.9rem;">CODE</p>
            <p style="margin: 5px 0;"><strong>.....</strong></p>
        </div>
        <?php endif; ?>
        
        <p class="text-center mt-20">
            <a href="index.php" class="text-link">← Back to Home</a>
        </p>
    </div>
</body>
</html>