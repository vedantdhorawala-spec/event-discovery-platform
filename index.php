
<?php
session_start();
require 'db.php';

$stmt = $pdo->prepare("SELECT * FROM events ORDER BY id DESC");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>EventHub - Discover Events</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h2>EventHub</h2>

    <nav>
        <a href="index.php">Home</a>
        <a href="#events">Events</a>
        <a href="bookings.php">Bookings</a>
        <a href="admin.php">Dashboard</a>
        <a href="manage_events.php">Manage Events</a>

        <?php if (isset($_SESSION['user_id'])) { ?>

            <span class="profile-name">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

            <a href="logout.php">Logout</a>

        <?php } else { ?>

            <a href="login.php">Login</a>
            <a href="register.php">Register</a>

        <?php } ?>
    </nav>
</header>

<section class="hero">
    <h1>Discover Amazing Events</h1>
    <p>Find events. Book tickets. Create memories.</p>
    <a href="#events" class="btn">Explore Events</a>
</section>

<section id="events">
    <h2>Upcoming Events</h2>

    <div class="event-grid">
        <?php foreach ($events as $event) { ?>
            <div class="event-card">
                <h3>
                    <?php echo htmlspecialchars($event['event name']); ?>
                </h3>

                <p>Category:
                    <?php echo htmlspecialchars($event['category']); ?>
                </p>

                <p>Date:
                    <?php echo htmlspecialchars($event['date']); ?>
                </p>

                <p>Location:
                    <?php echo htmlspecialchars($event['location']); ?>
                </p>

                <p>Price: ₹
                    <?php echo htmlspecialchars($event['price']); ?>
                </p>

                <a href="#booking" class="btn">Book Now</a>
            </div>
        <?php } ?>
    </div>
</section>

<?php if (isset($_GET['booking']) && $_GET['booking'] == 'success') { ?>
    <div class="success-message">
        Booking Successfully Done!
    </div>
<?php } ?>

<section id="booking">
    <h2>Book Your Tickets</h2>

    <form action="save_booking.php" method="POST">

        <input type="text"
               name="customer_name"
               placeholder="Your Name"
               required>

        <input type="email"
               name="email"
               placeholder="Your Email"
               required>

        <select name="event_id" required>
            <option value="">Select Event</option>

            <?php foreach ($events as $event) { ?>
                <option value="<?php echo $event['id']; ?>">
                    <?php echo htmlspecialchars($event['event name']); ?>
                </option>
            <?php } ?>
        </select>

        <input type="number"
               name="ticket_quantity"
               placeholder="Number of Tickets"
               min="1"
               required>

        <button type="submit">Confirm Booking</button>

    </form>
</section>

<footer>
    <p>© 2026 EventHub. All Rights Reserved.</p>
</footer>

</body>
</html>
