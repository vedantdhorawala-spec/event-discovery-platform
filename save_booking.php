
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Logged-in user details
    $name = $_SESSION['user_name'];
    $email = $_SESSION['user_email'];

    // Form details
    $event_id = isset($_POST['event_id'])
        ? (int) $_POST['event_id'] : 0;

    $quantity = isset($_POST['ticket_quantity'])
        ? (int) $_POST['ticket_quantity'] : 0;

    // Server-side validation
    if ($event_id <= 0 || $quantity <= 0 || $quantity > 10) {
        die("Invalid event or ticket quantity. Please try again.");
    }

    try {

        // Check whether event exists
        $check = $pdo->prepare(
            "SELECT id FROM events WHERE id = ?"
        );

        $check->execute(array($event_id));

        if (!$check->fetch(PDO::FETCH_ASSOC)) {
            die("Selected event does not exist.");
        }

        // Save booking
        $sql = "INSERT INTO bookings
        (`customer name`, email, `event id`,
        `ticket quantity`, `booking status`)
        VALUES (?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute(array(
            $name,
            $email,
            $event_id,
            $quantity,
            'Confirmed'
        ));

        header("Location: index.php?booking=success");
        exit;

    } catch (PDOException $e) {
        die("Booking Error: " . $e->getMessage());
    }

} else {
    header("Location: index.php");
    exit;
}
?>
