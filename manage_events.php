    
<?php
require 'db.php';

$message = "";

if (isset($_GET['error']) && $_GET['error'] == 'booked') {
    $message = "Cannot delete! This event has existing bookings.";
}

if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
    $message = "Event Deleted Successfully!";
}

// ADD EVENT
if (isset($_POST['add_event'])) {

    $name = trim($_POST['event_name']);
    $category = trim($_POST['category']);
    $date = $_POST['date'];
    $location = trim($_POST['location']);
    $price = $_POST['price'];

    if ($name != "" && $category != "" && $date != ""
        && $location != "" && is_numeric($price) && $price >= 0) {

        $stmt = $pdo->prepare(
            "INSERT INTO events
            (`event name`, category, `date`, location, price)
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->execute(array(
            $name, $category, $date, $location, $price
        ));

        $message = "Event Added Successfully!";
    } else {
        $message = "Please enter valid event details.";
    }
}

// DELETE EVENT
// DELETE EVENT
if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    // Check whether event has existing bookings
    $check = $pdo->prepare(
        "SELECT COUNT(*) FROM bookings WHERE `event id` = ?"
    );

    $check->execute(array($id));
    $bookingCount = $check->fetchColumn();

    if ($bookingCount > 0) {

        header("Location: manage_events.php?error=booked");
        exit;

    } else {

        $stmt = $pdo->prepare(
            "DELETE FROM events WHERE id = ?"
        );

        $stmt->execute(array($id));

        header("Location: manage_events.php?deleted=1");
        exit;
    }
}
// FETCH EVENTS
$stmt = $pdo->prepare("SELECT * FROM events ORDER BY id DESC");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Events - EventHub</title>
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
    <h2>Manage Events</h2>

    <p style="color:green; text-align:center;">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <form method="POST" action="manage_events.php">

        <h3>Add New Event</h3>

        <input type="text" name="event_name"
               placeholder="Event Name" required>

        <input type="text" name="category"
               placeholder="Category" required>

        <input type="date" name="date" required>

        <input type="text" name="location"
               placeholder="Location" required>

        <input type="number" name="price"
               placeholder="Ticket Price"
               min="0" step="0.01" required>

        <button type="submit" name="add_event">
            Add Event
        </button>

    </form>

    <h2>All Events</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Event Name</th>
            <th>Category</th>
            <th>Date</th>
            <th>Location</th>
            <th>Price</th>
            <th>Action</th>
        </tr>

        <?php foreach ($events as $event) { ?>
        <tr>
            <td><?php echo $event['id']; ?></td>
            <td><?php echo htmlspecialchars($event['event name']); ?></td>
            <td><?php echo htmlspecialchars($event['category']); ?></td>
            <td><?php echo htmlspecialchars($event['date']); ?></td>
            <td><?php echo htmlspecialchars($event['location']); ?></td>
            <td>₹<?php echo htmlspecialchars($event['price']); ?></td>
            <td>
                <a href="edit_event.php?id=<?php echo $event['id']; ?>">
                    Edit
                </a>

                |

                <a href="manage_events.php?delete=<?php echo $event['id']; ?>"
                   onclick="return confirm('Are you sure you want to delete this event?');">
                    Delete
                </a>
            </td>
        </tr>
        <?php } ?>

    </table>

</section>

</body>
</html>
