<?php
session_start();
require 'db.php';

$is_logged_in = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? '';


$register_error = '';
$register_success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? OR email=?");
    $stmt->execute([$username, $email]);
    $existing = $stmt->fetch();

    if($existing){
        $register_error = "Username or Email already exists!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username,email,password,role) VALUES (?,?,?,?)");
        $stmt->execute([$username, $email, $password, 'user']);
        $register_success = "Registration successful! You can now <a href='login.php'>login</a>.";
    }
}

?>

<html>
    <head>
    <title>Register - Paradise Travels</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    
    <body>
        <nav class="navbar">
            <div class="nav-content">
                <div class="logo">🌴 Paradise Travels</div>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="packages.php">Packages</a>
                    <?php if($is_logged_in && $role==='user'): ?>
                        <a href="user-dashboard.php">Book Now</a>
                    <?php endif; ?>
                    <?php if($is_logged_in && $role==='admin'): ?>
                        <a href="admin-dashboard.php">Book Now</a>
                    <?php else: ?>
                        <a href="login.php">Book Now</a>
                    <?php endif; ?>
                    <?php if(!$is_logged_in): ?>
                        <a href="login.php">Login</a>
                    <?php else: ?>
                        <a href="logout.php">Logout</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <div class="container">
            <h2>User Registration</h2>

            <?php if($register_error): ?>
                <div class="error"><?= htmlspecialchars($register_error) ?></div>
            <?php endif; ?>
            <?php if($register_success): ?>
                <div class="success"><?= $register_success ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>

            <div class="nav-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </body>
</html>
