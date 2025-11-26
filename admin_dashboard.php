<?php
require_once 'config.php';

if (!isAdminLoggedIn()) {
    redirect('admin_secret.php');
}

// Get statistics
$total_bookings_sql = "SELECT COUNT(*) as total FROM bookings";
$total_bookings = $conn->query($total_bookings_sql)->fetch_assoc()['total'];

$pending_bookings_sql = "SELECT COUNT(*) as total FROM bookings WHERE status = 'pending'";
$pending_bookings = $conn->query($pending_bookings_sql)->fetch_assoc()['total'];

$completed_bookings_sql = "SELECT COUNT(*) as total FROM bookings WHERE status = 'completed'";
$completed_bookings = $conn->query($completed_bookings_sql)->fetch_assoc()['total'];

$total_revenue_sql = "SELECT SUM(total_amount) as total FROM bookings WHERE status IN ('confirmed', 'completed')";
$total_revenue = $conn->query($total_revenue_sql)->fetch_assoc()['total'] ?? 0;

$total_users_sql = "SELECT COUNT(*) as total FROM users";
$total_users = $conn->query($total_users_sql)->fetch_assoc()['total'];

// Get recent bookings
$recent_bookings_sql = "SELECT b.*, u.full_name as user_name, s.service_name 
                        FROM bookings b 
                        JOIN users u ON b.user_id = u.user_id 
                        JOIN services s ON b.service_id = s.service_id 
                        ORDER BY b.created_at DESC LIMIT 10";
$recent_bookings = $conn->query($recent_bookings_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Groomily</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: linear-gradient(135deg, #fc5c7d 0%, #6a82fb 100%);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 10px 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .action-btn {
            display: inline-block;
            padding: 8px 15px;
            margin: 2px;
            border-radius: 8px;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-confirm {
            background: #28a745;
            color: white;
        }
        
        .btn-complete {
            background: #17a2b8;
            color: white;
        }
        
        .btn-cancel {
            background: #dc3545;
            color: white;
        }
        
        .action-btn:hover {
            opacity: 0.8;
            transform: scale(1.05);
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
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>! 👨‍💼</h1>
            <p style="color: #666; font-size: 1.1rem;">Manage all bookings and users from here</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-value"><?php echo $total_bookings; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-value"><?php echo $pending_bookings; ?></div>
                <div class="stat-label">Pending Bookings</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-value"><?php echo $completed_bookings; ?></div>
                <div class="stat-label">Completed</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-value">₱<?php echo number_format($total_revenue, 0); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        
        <div class="card">
            <h2>📋 Recent Bookings</h2>
            
            <?php if ($recent_bookings->num_rows > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Customer</th>
                                <th>Dog</th>
                                <th>Service</th>
                                <th>Date & Time</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($booking = $recent_bookings->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo $booking['booking_code']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['dog_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($booking['booking_date'])) . '<br>' . date('g:i A', strtotime($booking['booking_time'])); ?></td>
                                    <td>₱<?php echo number_format($booking['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($booking['status'] == 'pending'): ?>
                                            <a href="admin_update_status.php?id=<?php echo $booking['booking_id']; ?>&status=confirmed" 
                                               class="action-btn btn-confirm" 
                                               onclick="return confirm('Confirm this booking?')">✓ Confirm</a>
                                        <?php elseif ($booking['status'] == 'confirmed'): ?>
                                            <a href="admin_update_status.php?id=<?php echo $booking['booking_id']; ?>&status=completed" 
                                               class="action-btn btn-complete" 
                                               onclick="return confirm('Mark as completed?')">✓ Complete</a>
                                        <?php endif; ?>
                                        
                                        <?php if ($booking['status'] != 'cancelled' && $booking['status'] != 'completed'): ?>
                                            <a href="admin_update_status.php?id=<?php echo $booking['booking_id']; ?>&status=cancelled" 
                                               class="action-btn btn-cancel" 
                                               onclick="return confirm('Cancel this booking?')">✗ Cancel</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="admin_bookings.php" class="btn btn-secondary">View All Bookings →</a>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 30px;">No bookings yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>