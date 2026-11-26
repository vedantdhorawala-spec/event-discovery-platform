
<?php
require 'db.php';

if (!isset($_GET['id'])) {
    header("Location: manage_events.php");
    exit;
}

$id = (int) $_GET['id'];

// Fetch existing event
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute(array($id));
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Event not found!");
}

$message = "";

// Update event
if (isset($_POST['update_event'])) {

    $name = trim($_POST['event_name']);
    $category = trim($_POST['category']);
    $date = $_POST['date'];
    $location = trim($_POST['location']);
    $price = $_POST['price'];

    if ($name != "" && $category != "" && $date != ""
        && $location != "" && is_numeric($price) && $price >= 0) {

        $update = $pdo->prepare(
            "UPDATE events
             SET `event name` = ?, category = ?, `date` = ?,
                 location = ?, price = ?
             WHERE id = ?"
        );

        $update->execute(array(
            $name, $category, $date, $location, $price, $id
        ));

        header("Location: manage_events.php?updated=1");
        exit;

    } else {
        $message = "Please enter valid event details.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Event - EventHub</title>
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
    <h2>Edit Event</h2>

    <p style="color:red; text-align:center;">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <form method="POST">

        <input type="text" name="event_name"
               value="<?php echo htmlspecialchars($event['event name']); ?>"
               placeholder="Event Name" required>

        <input type="text" name="category"
               value="<?php echo htmlspecialchars($event['category']); ?>"
               placeholder="Category" required>

        <input type="date" name="date"
               value="<?php echo htmlspecialchars($event['date']); ?>"
               required>

        <input type="text" name="location"
               value="<?php echo htmlspecialchars($event['location']); ?>"
               placeholder="Location" required>

        <input type="number" name="price"
               value="<?php echo htmlspecialchars($event['price']); ?>"
               min="0" step="0.01" required>

        <button type="submit" name="update_event">
            Update Event
        </button>

    </form>
</section>

</body>
</html>
