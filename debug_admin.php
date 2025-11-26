<?php
require_once 'config.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Admin Login Debug</title>";
echo "<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
.container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
.section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
.success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
.error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
.info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
th { background: #007bff; color: white; }
textarea { width: 100%; padding: 10px; font-family: monospace; font-size: 12px; }
.btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
.btn:hover { background: #0056b3; }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>🔍 Admin Login Debugger</h1>";

// Step 1: Check database connection
echo "<div class='section'>";
echo "<h2>Step 1: Database Connection</h2>";
if ($conn->connect_error) {
    echo "<div class='error'>❌ Database connection failed: " . $conn->connect_error . "</div>";
} else {
    echo "<div class='success'>✅ Database connected successfully!</div>";
}
echo "</div>";

// Step 2: Check if admins table exists and show data
echo "<div class='section'>";
echo "<h2>Step 2: Check Admins Table</h2>";
$check_table = "SHOW TABLES LIKE 'admins'";
$table_result = $conn->query($check_table);

if ($table_result && $table_result->num_rows > 0) {
    echo "<div class='success'>✅ Admins table exists</div>";
    
    // Get admin data
    $admin_sql = "SELECT admin_id, admin_name, email, password, created_at FROM admins";
    $admin_result = $conn->query($admin_sql);
    
    if ($admin_result && $admin_result->num_rows > 0) {
        echo "<h3>Current Admin Records:</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Password (First 50 chars)</th><th>Created</th></tr>";
        while ($row = $admin_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['admin_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['admin_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td><code>" . htmlspecialchars(substr($row['password'], 0, 50)) . "...</code></td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='error'>❌ No admin records found in the table</div>";
    }
} else {
    echo "<div class='error'>❌ Admins table does not exist!</div>";
}
echo "</div>";

// Step 3: Test password verification
echo "<div class='section'>";
echo "<h2>Step 3: Password Verification Test</h2>";

$test_email = "admin@groomily.com";
$test_password = "admin123";

$stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
if ($stmt) {
    $stmt->bind_param("s", $test_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        echo "<div class='info'>📧 Email found: " . htmlspecialchars($admin['email']) . "</div>";
        
        $stored_password = $admin['password'];
        echo "<div class='info'>🔑 Stored password: <code>" . htmlspecialchars($stored_password) . "</code></div>";
        
        // Check if it's a bcrypt hash
        if (substr($stored_password, 0, 4) === '$2y$' || substr($stored_password, 0, 4) === '$2a$') {
            echo "<div class='success'>✅ Password appears to be properly hashed (bcrypt)</div>";
            
            // Test verification
            if (password_verify($test_password, $stored_password)) {
                echo "<div class='success'>✅ Password verification PASSED! 'admin123' matches the stored hash.</div>";
                echo "<div class='success'><strong>Your login should work now!</strong></div>";
            } else {
                echo "<div class='error'>❌ Password verification FAILED! 'admin123' does not match the stored hash.</div>";
                echo "<div class='info'>This means the hash in the database is for a different password.</div>";
            }
        } else {
            echo "<div class='error'>❌ Password is NOT hashed! It's stored as plain text: <code>" . htmlspecialchars($stored_password) . "</code></div>";
            echo "<div class='info'>The login will fail because password_verify() expects a hashed password.</div>";
        }
    } else {
        echo "<div class='error'>❌ No admin found with email: " . htmlspecialchars($test_email) . "</div>";
    }
    $stmt->close();
} else {
    echo "<div class='error'>❌ Failed to prepare statement</div>";
}
echo "</div>";

// Step 4: Provide fix
echo "<div class='section'>";
echo "<h2>Step 4: Fix the Issue</h2>";

// Generate new hash
$new_password = "admin123";
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);

echo "<div class='info'><strong>Option 1: Run this SQL query to fix the password:</strong></div>";
echo "<textarea rows='4' onclick='this.select()'>UPDATE admins SET password = '$new_hash' WHERE email = 'admin@groomily.com';</textarea>";

echo "<div class='info' style='margin-top: 20px;'><strong>Option 2: Use this form to update automatically:</strong></div>";

if (isset($_POST['fix_password'])) {
    $update_sql = "UPDATE admins SET password = ? WHERE email = 'admin@groomily.com'";
    $update_stmt = $conn->prepare($update_sql);
    if ($update_stmt) {
        $update_stmt->bind_param("s", $new_hash);
        if ($update_stmt->execute()) {
            echo "<div class='success'>✅ Password updated successfully! Try logging in now with:<br>";
            echo "Email: admin@groomily.com<br>";
            echo "Password: admin123</div>";
        } else {
            echo "<div class='error'>❌ Failed to update password: " . $update_stmt->error . "</div>";
        }
        $update_stmt->close();
    }
}

echo "<form method='POST'>";
echo "<button type='submit' name='fix_password' class='btn'>🔧 Fix Password Automatically</button>";
echo "</form>";

echo "</div>";

// Step 5: Test login process
echo "<div class='section'>";
echo "<h2>Step 5: Test Login</h2>";
echo "<form method='POST'>";
echo "<p><label>Email:</label><br><input type='email' name='test_email' value='admin@groomily.com' style='width: 100%; padding: 8px;'></p>";
echo "<p><label>Password:</label><br><input type='password' name='test_password' value='admin123' style='width: 100%; padding: 8px;'></p>";
echo "<button type='submit' name='test_login' class='btn'>🧪 Test Login</button>";
echo "</form>";

if (isset($_POST['test_login'])) {
    $email = $_POST['test_email'];
    $password = $_POST['test_password'];
    
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            echo "<div class='success'>✅✅✅ LOGIN SUCCESSFUL! ✅✅✅<br>";
            echo "Admin: " . htmlspecialchars($admin['admin_name']) . "<br>";
            echo "You can now login at <a href='admin_login.php'>admin_login.php</a></div>";
        } else {
            echo "<div class='error'>❌ Password verification failed!</div>";
        }
    } else {
        echo "<div class='error'>❌ Email not found!</div>";
    }
    $stmt->close();
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>📝 Next Steps</h2>";
echo "<ol>";
echo "<li>Click the <strong>'Fix Password Automatically'</strong> button above</li>";
echo "<li>Then click <strong>'Test Login'</strong> to verify it works</li>";
echo "<li>If successful, go to <a href='admin_secret.php'>admin_secret.php</a></li>";
echo "<li>Enter secret code: <strong>WOOF2024</strong></li>";
echo "<li>Login with Email: <strong>admin@groomily.com</strong> and Password: <strong>admin123</strong></li>";
echo "<li><strong>Delete this debug file after fixing!</strong></li>";
echo "</ol>";
echo "</div>";

echo "</div>";
echo "</body></html>";
?>