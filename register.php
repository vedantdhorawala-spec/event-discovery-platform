
<?php
require 'db.php';

$message = "";

if (isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name == "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($password) < 6) {

        $message = "Please enter valid details. Password must be at least 6 characters.";

    } else {

        // Check if email already exists
        $check = $pdo->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->execute(array($email));

        if ($check->rowCount() > 0) {

            $message = "Email already registered!";

        } else {

            // Hash password
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, password)
                 VALUES (?, ?, ?)"
            );

            $stmt->execute(array(
                $name,
                $email,
                $hashedPassword
            ));

            header("Location: login.php?registered=1");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - EventHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h2>EventHub</h2>
    <nav>
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
    </nav>
</header>

<section>
    <h2>Create Account</h2>

    <p style="color:red; text-align:center;">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <form method="POST" action="register.php">

        <input type="text"
               name="name"
               placeholder="Full Name"
               required>

        <input type="email"
               name="email"
               placeholder="Email Address"
               required>

        <input type="password"
               name="password"
               placeholder="Password (Minimum 6 Characters)"
               minlength="6"
               required>

        <button type="submit" name="register">
            Register
        </button>

        <p>Already have an account?
            <a href="login.php">Login Here</a>
        </p>

    </form>
</section>

</body>
</html>
