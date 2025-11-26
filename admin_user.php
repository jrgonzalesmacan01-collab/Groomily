<?php
require_once 'config.php';

if (!isAdminLoggedIn()) {
    redirect('admin_secret.php');
}

$users_sql = "SELECT u.*, 
              (SELECT COUNT(*) FROM bookings WHERE user_id = u.user_id) as total_bookings,
              (SELECT SUM(total_amount) FROM bookings WHERE user_id = u.user_id AND status IN ('confirmed', 'completed')) as total_spent
              FROM users u 
              ORDER BY u.created_at DESC";
$users_result = $conn->query($users_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: linear-gradient(135deg, #fc5c7d 0%, #6a82fb 100%);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-content">
            <div class="logo" style="font-size: 1.8rem;">🔧 Groomily Admin</div>
            <ul class="nav-links">
                <li><a href="admin_dashboard.php">Dashboard 📊</a></li>
                <li><a href="admin_bookings.php">All Bookings 📋</a></li>
                <li><a href="admin_users.php">Users 👥</a></li>
                <li><a href="logout.php?admin=1">Logout 👋</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <div class="card">
            <h1>👥 Registered Users</h1>
            <p style="color: #666;">View and manage all registered customers</p>
        </div>
        
        <?php if ($users_result->num_rows > 0): ?>
            <div class="card">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Total Bookings</th>
                                <th>Total Spent</th>
                                <th>Registered Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users_result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo $user['user_id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                    <td><?php echo $user['total_bookings']; ?></td>
                                    <td><strong>₱<?php echo number_format($user['total_spent'] ?? 0, 2); ?></strong></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <p style="text-align: center; color: #666; padding: 50px;">No users registered yet.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>