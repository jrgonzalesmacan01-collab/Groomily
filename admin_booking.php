<?php
require_once 'config.php';

if (!isAdminLoggedIn()) {
    redirect('admin_secret.php');
}

// Filter
$status_filter = isset($_GET['status']) ? clean($_GET['status']) : 'all';
$search = isset($_GET['search']) ? clean($_GET['search']) : '';

// Build query
$where = "1=1";
if ($status_filter != 'all') {
    $where .= " AND b.status = '$status_filter'";
}
if (!empty($search)) {
    $where .= " AND (b.booking_code LIKE '%$search%' OR b.dog_name LIKE '%$search%' OR u.full_name LIKE '%$search%')";
}

$bookings_sql = "SELECT b.*, u.full_name as user_name, u.email, u.phone, s.service_name 
                 FROM bookings b 
                 JOIN users u ON b.user_id = u.user_id 
                 JOIN services s ON b.service_id = s.service_id 
                 WHERE $where
                 ORDER BY b.booking_date DESC, b.booking_time DESC";
$bookings_result = $conn->query($bookings_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings - Admin</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: linear-gradient(135deg, #fc5c7d 0%, #6a82fb 100%);
        }
        
        .filters {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .filter-btn {
            padding: 8px 20px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--dark-color);
        }
        
        .filter-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .search-box {
            flex: 1;
            min-width: 250px;
        }
        
        .action-btn {
            display: inline-block;
            padding: 6px 12px;
            margin: 2px;
            border-radius: 6px;
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-confirm { background: #28a745; color: white; }
        .btn-complete { background: #17a2b8; color: white; }
        .btn-cancel { background: #dc3545; color: white; }
        .btn-view { background: #6c757d; color: white; }
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
            <h1>📋 All Bookings</h1>
            <p style="color: #666;">Manage and track all dog grooming appointments</p>
            
            <div class="filters">
                <a href="?status=all" class="filter-btn <?php echo $status_filter == 'all' ? 'active' : ''; ?>">All</a>
                <a href="?status=pending" class="filter-btn <?php echo $status_filter == 'pending' ? 'active' : ''; ?>">Pending</a>
                <a href="?status=confirmed" class="filter-btn <?php echo $status_filter == 'confirmed' ? 'active' : ''; ?>">Confirmed</a>
                <a href="?status=completed" class="filter-btn <?php echo $status_filter == 'completed' ? 'active' : ''; ?>">Completed</a>
                <a href="?status=cancelled" class="filter-btn <?php echo $status_filter == 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
                
                <form method="GET" class="search-box" style="display: flex; gap: 10px;">
                    <input type="hidden" name="status" value="<?php echo $status_filter; ?>">
                    <input type="text" name="search" placeholder="Search by code, dog name, or customer..." 
                           value="<?php echo htmlspecialchars($search); ?>" style="flex: 1;">
                    <button type="submit" class="btn btn-secondary">🔍 Search</button>
                </form>
            </div>
        </div>
        
        <?php if ($bookings_result->num_rows > 0): ?>
            <div class="card">
                <div class="table-container">
                    <table style="font-size: 0.9rem;">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Dog Info</th>
                                <th>Service</th>
                                <th>Date & Time</th>
                                <th>Payment</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo $booking['booking_code']; ?></strong></td>
                                    <td>
                                        <?php echo htmlspecialchars($booking['user_name']); ?><br>
                                        <small style="color: #999;"><?php echo htmlspecialchars($booking['email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['owner_phone']); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($booking['dog_name']); ?></strong><br>
                                        <small><?php echo $booking['dog_age']; ?>y, <?php echo htmlspecialchars($booking['dog_breed']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?><br>
                                        <small><?php echo date('g:i A', strtotime($booking['booking_time'])); ?></small>
                                    </td>
                                    <td>
                                        <?php echo ucfirst($booking['payment_method']); ?><br>
                                        <small class="badge badge-<?php echo $booking['payment_status']; ?>">
                                            <?php echo ucfirst($booking['payment_status']); ?>
                                        </small>
                                    </td>
                                    <td><strong>₱<?php echo number_format($booking['total_amount'], 2); ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($booking['status'] == 'pending'): ?>
                                            <a href="admin_update_status.php?id=<?php echo $booking['booking_id']; ?>&status=confirmed&return=bookings" 
                                               class="action-btn btn-confirm" 
                                               onclick="return confirm('Confirm this booking?')">✓</a>
                                        <?php elseif ($booking['status'] == 'confirmed'): ?>
                                            <a href="admin_update_status.php?id=<?php echo $booking['booking_id']; ?>&status=completed&return=bookings" 
                                               class="action-btn btn-complete" 
                                               onclick="return confirm('Mark as completed?')">✓</a>
                                        <?php endif; ?>
                                        
                                        <?php if ($booking['status'] != 'cancelled' && $booking['status'] != 'completed'): ?>
                                            <a href="admin_update_status.php?id=<?php echo $booking['booking_id']; ?>&status=cancelled&return=bookings" 
                                               class="action-btn btn-cancel" 
                                               onclick="return confirm('Cancel this booking?')">✗</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <p style="text-align: center; color: #666; padding: 50px;">
                    No bookings found matching your criteria.
                </p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>