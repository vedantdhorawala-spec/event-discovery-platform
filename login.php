
<?php
session_start();
require 'db.php';

$message = "";

if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $message = "Registration successful! Please login.";
}

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password == "") {

        $message = "Please enter valid email and password.";

    } else {

        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE email = ?"
        );

        $stmt->execute(array($email));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];

            header("Location: index.php");
            exit;

        } else {

            $message = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - EventHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h2>EventHub</h2>
    <nav>
        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
    </nav>
</header>

<section>
    <h2>User Login</h2>

    <p style="color:green; text-align:center;">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <form method="POST" action="login.php">

        <input type="email"
               name="email"
               placeholder="Email Address"
               required>

        <input type="password"
               name="password"
               placeholder="Password"
               required>

        <button type="submit" name="login">
            Login
        </button>

        <p>Don't have an account?
            <a href="register.php">Register Here</a>
        </p>

    </form>
</section>

</body>
</html>
