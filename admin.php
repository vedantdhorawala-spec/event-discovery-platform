
<?php
require 'db.php';

$totalEvents = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();

$totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();

$confirmedBookings = $pdo->query(
    "SELECT COUNT(*) FROM bookings WHERE `booking status` = 'Confirmed'"
)->fetchColumn();

$cancelledBookings = $pdo->query(
    "SELECT COUNT(*) FROM bookings WHERE `booking status` = 'Cancelled'"
)->fetchColumn();

$recentBookings = $pdo->query("
    SELECT b.booking_id,
           b.`customer name` AS customer_name,
           e.`event name` AS event_name,
           b.`booking status` AS booking_status
    FROM bookings b
    LEFT JOIN events e ON b.`event id` = e.id
    ORDER BY b.booking_id DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - EventHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h2>EventHub Admin</h2>
    <nav>
        <a href="index.php">Home</a>
        <a href="bookings.php">Manage Bookings</a>
    </nav>
</header>

<section class="dashboard">
    <h2>Admin Dashboard</h2>

    <div class="dashboard-grid">

        <div class="dashboard-card">
            <h3>Total Events</h3>
            <h1><?php echo $totalEvents; ?></h1>
        </div>

        <div class="dashboard-card">
            <h3>Total Bookings</h3>
            <h1><?php echo $totalBookings; ?></h1>
        </div>

        <div class="dashboard-card">
            <h3>Confirmed Bookings</h3>
            <h1><?php echo $confirmedBookings; ?></h1>
        </div>

        <div class="dashboard-card">
            <h3>Cancelled Bookings</h3>
            <h1><?php echo $cancelledBookings; ?></h1>
        </div>

    </div>
    <h2>Recent Bookings</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Event</th>
        <th>Status</th>
    </tr>

    <?php foreach ($recentBookings as $booking) { ?>
    <tr>
        <td><?php echo $booking['booking_id']; ?></td>
        <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
        <td><?php echo htmlspecialchars($booking['event_name']); ?></td>
        <td><?php echo htmlspecialchars($booking['booking_status']); ?></td>
    </tr>
    <?php } ?>
</table>

<br>
<a href="bookings.php" class="btn">Manage All Bookings</a>
</section>

</body>
</html>
