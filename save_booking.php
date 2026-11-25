
<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['customer_name']);
    $email = trim($_POST['email']);
    $event_id = (int) $_POST['event_id'];
    $quantity = (int) $_POST['ticket_quantity'];

    if ($name == "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || $event_id <= 0 || $quantity <= 0) {
        die("Please enter valid booking details.");
    }

    try {
        $sql = "INSERT INTO bookings
        (`customer name`, email, `event id`,
        `ticket quantity`, `booking status`)
        VALUES (?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            $name, $email, $event_id, $quantity, 'Confirmed'
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
