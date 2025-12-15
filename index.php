<?php
session_start();
require 'db.php';

$is_logged_in = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? '';
?>

<html>
    <head>
    <title>Paradise Travels - Your Dream Vacation</title>
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

        <div class="hero">
            <div class="hero-content">
                <h1>Explore the World with Us</h1>
                <p>Unforgettable journeys at unbeatable prices</p>
                <?php if($is_logged_in && $role==='user'): ?>
                    <a href="user-dashboard.php" class="btn">Discover Packages</a>
                <?php else: ?>
                    <a href="packages.php" class="btn">Discover Packages</a>
                <?php endif; ?>
            </div>
        </div>

    </body>
</html>
