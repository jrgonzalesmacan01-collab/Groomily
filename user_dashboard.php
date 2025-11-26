<?php


require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
    
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// 🔒 Clear the login flag after loading dashboard
if (isset($_SESSION['just_logged_in'])) {
    unset($_SESSION['just_logged_in']);
}
}

// Handle cancel booking
if (isset($_GET['cancel']) && isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    // Verify booking belongs to user and can be cancelled
    $verify_sql = "SELECT booking_date, booking_time, status FROM bookings 
                   WHERE booking_id = $booking_id AND user_id = $user_id 
                   AND status IN ('pending', 'confirmed')";
    $verify_result = $conn->query($verify_sql);
    
    if ($verify_result->num_rows > 0) {
        $booking = $verify_result->fetch_assoc();
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Update booking status
            $cancel_sql = "UPDATE bookings SET status = 'cancelled' WHERE booking_id = $booking_id";
            $conn->query($cancel_sql);
            
            // Free up the slot
            $update_slot = "UPDATE booking_slots 
                           SET current_bookings = current_bookings - 1 
                           WHERE booking_date = '{$booking['booking_date']}' 
                           AND booking_time = '{$booking['booking_time']}'";
            $conn->query($update_slot);
            
            $conn->commit();
            $_SESSION['success'] = 'Booking cancelled successfully!';
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error'] = 'Failed to cancel booking.';
        }
    } else {
        $_SESSION['error'] = 'Booking not found or cannot be cancelled.';
    }
    
    redirect('user_dashboard.php');
    
}

// Get user bookings
$user_id = $_SESSION['user_id'];
$pending_sql = "SELECT b.*, s.service_name FROM bookings b 
                JOIN services s ON b.service_id = s.service_id 
                WHERE b.user_id = $user_id AND b.status IN ('pending', 'confirmed')
                ORDER BY b.booking_date DESC, b.booking_time DESC";
$pending_result = $conn->query($pending_sql);

$history_sql = "SELECT b.*, s.service_name FROM bookings b 
                JOIN services s ON b.service_id = s.service_id 
                WHERE b.user_id = $user_id AND b.status IN ('completed', 'cancelled')
                ORDER BY b.booking_date DESC, b.booking_time DESC LIMIT 10";
$history_result = $conn->query($history_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Groomily</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .action-btn {
            display: inline-block;
            padding: 6px 15px;
            margin: 2px;
            border-radius: 6px;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-cancel {
            background: #dc3545;
            color: white;
        }
        
        .btn-cancel:hover {
            background: #c82333;
            transform: scale(1.05);
        }

        .btn-view {
            background: #6c757d;
            color: white;
        }

        .btn-view:hover {
            background: #5a6268;
            transform: scale(1.05);
        }

        .booking-details {
            font-size: 0.9rem;
        }

        .booking-details strong {
            color: var(--dark-color);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-content">
            <div class="logo" style="font-size: 1.8rem;">Groomily</div>
            <ul class="nav-links">
                <li><a href="user_dashboard.php">Dashboard 🏠</a></li>
                <li><a href="booking.php">New Booking 📅</a></li>
                <li><a href="logout.php">Logout 👋</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <div class="card">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! 🐕</h1>
            <p style="color: #666; font-size: 1.1rem;">Manage your dog grooming appointments</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="booking.php" class="btn btn-primary" style="font-size: 1.1rem; padding: 15px 40px;">
                📅 Book New Appointment
            </a>
        </div>
        
        <div class="card">
            <h2>📋 Active Bookings</h2>
            <p style="color: #666; margin-bottom: 20px;">Your upcoming and confirmed appointments</p>
            
            <?php if ($pending_result->num_rows > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking Code</th>
                                <th>Dog Info</th>
                                <th>Service</th>
                                <th>Date & Time</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($booking = $pending_result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo $booking['booking_code']; ?></strong></td>
                                    <td class="booking-details">
                                        <strong><?php echo htmlspecialchars($booking['dog_name']); ?></strong><br>
                                        <small style="color: #999;">
                                            <?php echo $booking['dog_age']; ?>y, <?php echo htmlspecialchars($booking['dog_breed']); ?>
                                        </small>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                    <td>
                                        <strong><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></strong><br>
                                        <small><?php echo date('g:i A', strtotime($booking['booking_time'])); ?></small>
                                    </td>
                                    <td><strong>₱<?php echo number_format($booking['total_amount'], 2); ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button onclick="viewBooking(<?php echo $booking['booking_id']; ?>)" 
                                                class="action-btn btn-view" title="View Details">
                                            👁️ View
                                        </button>
                                        <a href="?cancel=1&id=<?php echo $booking['booking_id']; ?>" 
                                           class="action-btn btn-cancel"
                                           onclick="return confirm('Are you sure you want to cancel this booking?\n\nBooking: <?php echo $booking['booking_code']; ?>\nDog: <?php echo htmlspecialchars($booking['dog_name']); ?>\nDate: <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?>');"
                                           title="Cancel Booking">
                                            ✗ Cancel
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 30px;">
                    No active bookings. <a href="booking.php" class="text-link">Book your first appointment!</a>
                </p>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>📚 Booking History</h2>
            <p style="color: #666; margin-bottom: 20px;">Your past and cancelled appointments</p>
            
            <?php if ($history_result->num_rows > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking Code</th>
                                <th>Dog Info</th>
                                <th>Service</th>
                                <th>Date & Time</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($booking = $history_result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo $booking['booking_code']; ?></strong></td>
                                    <td class="booking-details">
                                        <strong><?php echo htmlspecialchars($booking['dog_name']); ?></strong><br>
                                        <small style="color: #999;">
                                            <?php echo $booking['dog_age']; ?>y, <?php echo htmlspecialchars($booking['dog_breed']); ?>
                                        </small>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?><br>
                                        <small><?php echo date('g:i A', strtotime($booking['booking_time'])); ?></small>
                                    </td>
                                    <td>₱<?php echo number_format($booking['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 30px;">No booking history yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div id="bookingModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 20px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0;">Booking Details</h2>
                <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">✕</button>
            </div>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        function viewBooking(bookingId) {
            // In a real application, fetch booking details via AJAX
            // For now, we'll show a simple modal
            const modal = document.getElementById('bookingModal');
            const content = document.getElementById('modalContent');
            
            content.innerHTML = '<p style="text-align: center;">Loading booking details...</p>';
            modal.style.display = 'block';
            
            // Simulate loading (in production, use fetch/AJAX here)
            setTimeout(() => {
                content.innerHTML = `
                    <div style="text-align: center; padding: 20px;">
                        <p style="color: #666;">Feature coming soon!</p>
                        <p style="margin-top: 10px;">Full booking details will be displayed here.</p>
                        <button onclick="closeModal()" class="btn btn-primary" style="margin-top: 20px;">Close</button>
                    </div>
                `;
            }, 500);
        }

        function closeModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('bookingModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
     <script>
        function viewBooking(bookingId) {
            const modal = document.getElementById('bookingModal');
            const content = document.getElementById('modalContent');
            
            content.innerHTML = '<p style="text-align: center;">Loading booking details...</p>';
            modal.style.display = 'block';
            
            setTimeout(() => {
                content.innerHTML = `
                    <div style="text-align: center; padding: 20px;">
                        <p style="color: #666;">Feature coming soon!</p>
                        <p style="margin-top: 10px;">Full booking details will be displayed here.</p>
                        <button onclick="closeModal()" class="btn btn-primary" style="margin-top: 20px;">Close</button>
                    </div>
                `;
            }, 500);
        }

        function closeModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }

        document.getElementById('bookingModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // 🔒 CRITICAL: Block back button to prevent returning to login
        (function() {
            window.history.pushState(null, '', window.location.href);
            
            window.addEventListener('popstate', function() {
                window.history.pushState(null, '', window.location.href);
            });
        })();

        // 🔒 Force reload if page loaded from cache (after logout)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                window.location.reload(true);
            }
        });
    </script>
</body>
</html>
</body>
</html>