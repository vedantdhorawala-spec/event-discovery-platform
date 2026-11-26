
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$userEmail = $_SESSION['user_email'];

// Update booking status
if (isset($_POST['update_status'])) {

    $id = (int) $_POST['booking_id'];
    $status = $_POST['booking_status'];

    if (in_array($status, array('Confirmed', 'Pending', 'Cancelled'))) {

        $stmt = $pdo->prepare(
            "UPDATE bookings
             SET `booking status` = ?
             WHERE booking_id = ? AND email = ?"
        );

        $stmt->execute(array($status, $id, $userEmail));
    }

    header("Location: bookings.php");
    exit;
}

// Cancel booking
if (isset($_POST['cancel_booking'])) {

    $id = (int) $_POST['booking_id'];

    $stmt = $pdo->prepare(
        "UPDATE bookings
         SET `booking status` = 'Cancelled'
         WHERE booking_id = ? AND email = ?"
    );

    $stmt->execute(array($id, $userEmail));

    header("Location: bookings.php");
    exit;
}

// Search bookings
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT
            b.booking_id,
            b.`customer name` AS customer_name,
            b.email,
            e.`event name` AS event_name,
            b.`ticket quantity` AS ticket_quantity,
            b.`booking status` AS booking_status
        FROM bookings b
        LEFT JOIN events e ON b.`event id` = e.id
        WHERE b.email = ?
        AND (
            b.`customer name` LIKE ?
            OR b.email LIKE ?
        )
        ORDER BY b.booking_id DESC";

$stmt = $pdo->prepare($sql);

$stmt->execute(array(
    $userEmail,
    '%' . $search . '%',
    '%' . $search . '%'
));

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings - EventHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h2>EventHub</h2>

    <nav>
        <a href="index.php">Home</a>
        <a href="index.php#events">Events</a>
        <a href="bookings.php">Bookings</a>
        <a href="admin.php">Dashboard</a>
        <a href="manage_events.php">Manage Events</a>
    </nav>
</header>

<section>
    <h2>Manage Bookings</h2>

    <p>
        Logged in as:
        <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
    </p>

    <form method="GET" action="bookings.php">
        <input
            type="text"
            name="search"
            placeholder="Search customer name or email"
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">Search</button>
    </form>

    <br>

    <table border="1" cellpadding="10" cellspacing="0"
           style="width:100%; background:white; border-collapse:collapse;">

        <tr style="background:#6842a5; color:white;">
            <th>ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Event</th>
            <th>Tickets</th>
            <th>Status</th>
            <th>Update</th>
            <th>Cancel</th>
        </tr>

        <?php if (count($bookings) > 0) { ?>

            <?php foreach ($bookings as $booking) { ?>

                <tr>
                    <td>
                        <?php echo $booking['booking_id']; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($booking['customer_name']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($booking['email']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($booking['event_name']); ?>
                    </td>

                    <td>
                        <?php echo $booking['ticket_quantity']; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($booking['booking_status']); ?>
                    </td>

                    <td>
                        <form method="POST" action="bookings.php">

                            <input type="hidden" name="booking_id"
                                   value="<?php echo $booking['booking_id']; ?>">

                            <select name="booking_status">
                                <option value="Confirmed"
                                    <?php if ($booking['booking_status'] == 'Confirmed') echo 'selected'; ?>>
                                    Confirmed
                                </option>

                                <option value="Pending"
                                    <?php if ($booking['booking_status'] == 'Pending') echo 'selected'; ?>>
                                    Pending
                                </option>

                                <option value="Cancelled"
                                    <?php if ($booking['booking_status'] == 'Cancelled') echo 'selected'; ?>>
                                    Cancelled
                                </option>
                            </select>

                            <button type="submit" name="update_status">
                                Update
                            </button>

                        </form>
                    </td>

                    <td>
                        <form method="POST" action="bookings.php">

                            <input type="hidden" name="booking_id"
                                   value="<?php echo $booking['booking_id']; ?>">

                            <button type="submit" name="cancel_booking">
                                Cancel
                            </button>

                        </form>
                    </td>
                </tr>

            <?php } ?>

        <?php } else { ?>

            <tr>
                <td colspan="8" align="center">
                    No Bookings Found
                </td>
            </tr>

        <?php } ?>

    </table>
</section>

</body>
</html>
