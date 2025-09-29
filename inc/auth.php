<?php
require_once "includes/db.php";
require_once "includes/auth.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    if (login_user($pdo, $username, $password)) {
        header("Location: index.php");
        exit;
    } else {
        $message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Asset Management</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Asset Management System</h1>
</header>

<div class="container">
    <h2>Login</h2>
    <?php if ($message): ?>
        <p style="color:red;"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
