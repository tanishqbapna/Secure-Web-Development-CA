<?php
session_start();
require 'db.php';

$is_logged_in = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? '';

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $pdo->query($sql);

    $user = $result->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
    header('Location: admin-dashboard.php?user_id=' . urlencode($user['id']));
} else {
    header('Location: user-dashboard.php?user_id=' . urlencode($user['id']));
}
exit;

    } else {
        $login_error = "Invalid username or password!";
    }
}

?>

<html>
    <head>
    <title>Login - Paradise Travels</title>
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
                        <a href="user-dashboard.php">Dashboard</a>
                    <?php endif; ?>
                    <?php if($is_logged_in && $role==='admin'): ?>
                        <a href="admin-dashboard.php">Dashboard</a>
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
            <h2>Login</h2>

            <?php if($login_error): ?>
                <div class="error"><?= htmlspecialchars($login_error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
            <div class="nav-link">
            New user? <a href="register.php">Register here</a>
            </div>
        </div>

    </body>
</html>
