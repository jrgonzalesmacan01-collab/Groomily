<?php
// Password Generator for Groomily Admin

$password = "admin123";
$hashed = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash Generator</h2>";
echo "<p><strong>Plain Password:</strong> " . htmlspecialchars($password) . "</p>";
echo "<p><strong>Hashed Password:</strong></p>";
echo "<textarea style='width:100%; height:100px; font-family:monospace;'>" . $hashed . "</textarea>";

echo "<hr>";
echo "<h3>SQL Query to Update Admin Password:</h3>";
echo "<textarea style='width:100%; height:150px; font-family:monospace;'>";
echo "USE `groomily`;\n\n";
echo "UPDATE `admins` \n";
echo "SET `password` = '$hashed' \n";
echo "WHERE `email` = 'admin@groomily.com';\n";
echo "</textarea>";

echo "<hr>";
echo "<p><strong>Instructions:</strong></p>";
echo "<ol>";
echo "<li>Copy the SQL query above</li>";
echo "<li>Go to phpMyAdmin</li>";
echo "<li>Click on 'groomily' database</li>";
echo "<li>Click SQL tab</li>";
echo "<li>Paste and execute the query</li>";
echo "<li>Delete this file after use for security!</li>";
echo "</ol>";
?>