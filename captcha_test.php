<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CAPTCHA Diagnostic Test</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 20px 0; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>🔍 reCAPTCHA Diagnostic Test</h1>
    
    <div class="box">
        <h2>1. Configuration Check</h2>
        <p><strong>Site Key:</strong> <code><?php echo RECAPTCHA_SITE_KEY; ?></code></p>
        <p><strong>Secret Key (first 20 chars):</strong> <code><?php echo substr(RECAPTCHA_SECRET_KEY, 0, 20); ?>...</code></p>
        <p><strong>Expected Site Key:</strong> <code>6Lci2RgsAAAAAKtaY5EXbUqpWiNrGQUk_r6s-jyJ</code></p>
        <p class="<?php echo (RECAPTCHA_SITE_KEY === '6Lci2RgsAAAAAKtaY5EXbUqpWiNrGQUk_r6s-jyJ') ? 'success' : 'error'; ?>">
            <?php echo (RECAPTCHA_SITE_KEY === '6Lci2RgsAAAAAKtaY5EXbUqpWiNrGQUk_r6s-jyJ') ? '✅ Site Key MATCHES!' : '❌ Site Key DOES NOT MATCH!'; ?>
        </p>
    </div>
    
    <div class="box">
        <h2>2. CAPTCHA Widget Test</h2>
        <form method="POST">
            <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
            <br>
            <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Test Submit</button>
        </form>
    </div>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        echo '<div class="box">';
        echo '<h2>3. Verification Results</h2>';
        
        $captcha = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
        
        echo "<p><strong>CAPTCHA Response Received:</strong> " . (empty($captcha) ? '<span class="error">❌ NO</span>' : '<span class="success">✅ YES</span>') . "</p>";
        
        if (!empty($captcha)) {
            echo "<p><strong>Response Token (first 50 chars):</strong> <code>" . substr($captcha, 0, 50) . "...</code></p>";
            
            // Manual verification
            $secret = RECAPTCHA_SECRET_KEY;
            $url = "https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$captcha}";
            
            echo "<p><strong>Verification URL:</strong> <code style='word-break: break-all;'>" . htmlspecialchars($url) . "</code></p>";
            
            $verify = file_get_contents($url);
            echo "<p><strong>Google Response:</strong> <code>" . htmlspecialchars($verify) . "</code></p>";
            
            $captcha_success = json_decode($verify);
            
            if ($captcha_success->success) {
                echo '<p class="success"><strong>✅✅✅ CAPTCHA VERIFICATION SUCCESSFUL! ✅✅✅</strong></p>';
            } else {
                echo '<p class="error"><strong>❌ CAPTCHA VERIFICATION FAILED</strong></p>';
                if (isset($captcha_success->{'error-codes'})) {
                    echo '<p class="error">Error codes: ' . implode(', ', $captcha_success->{'error-codes'}) . '</p>';
                }
            }
        } else {
            echo '<p class="error">No CAPTCHA response was submitted. Did you check the box?</p>';
        }
        
        echo '</div>';
    }
    ?>
    
    <div class="box">
        <h2>4. Quick Fixes</h2>
        <ul>
            <li>Make sure you clicked "SUBMIT" on the Google reCAPTCHA settings page</li>
            <li>Verify "localhost" is in your domains (no port number needed)</li>
            <li>Try opening this page in an incognito/private window</li>
            <li>Clear your browser cache completely</li>
        </ul>
    </div>
    
    <div class="box">
        <h2>5. Your Access URL</h2>
        <p><strong>Current URL:</strong> <code><?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?></code></p>
        <p><strong>Server Name:</strong> <code><?php echo $_SERVER['SERVER_NAME']; ?></code></p>
        <p><strong>Server Port:</strong> <code><?php echo $_SERVER['SERVER_PORT']; ?></code></p>
    </div>
</body>
</html>