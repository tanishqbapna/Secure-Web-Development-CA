<?php
session_start();
require 'db.php';

$packages = $pdo->query("SELECT * FROM packages ORDER BY created_at DESC")->fetchAll();

$is_logged_in = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? '';
?>


<html>

    <head>
    <title>Travel Packages - Paradise Travels</title>
    <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f0f2f5; color:#333; margin:0; padding:0; }
    .navbar { font-weight:bold; background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); padding:1rem 2rem; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
    .nav-content { max-width:1200px; margin:0 auto; display:flex; justify-content:space-between; align-items:center; }
    .logo { font-size:1.8rem; font-weight:bold; color:white; }
    .nav-links { display:flex; gap:2rem; }
    .nav-links a { color:white; text-decoration:none; transition:opacity 0.3s; }
    .nav-links a:hover { opacity:0.8; }
    .container { max-width:1200px; margin:2rem auto; padding:2rem; }
    .section-title { text-align:center; font-size:2.5rem; margin-bottom:3rem; color:#667eea; }
    .packages-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(300px,1fr)); gap:2rem; }
    .package-card { background:white; border-radius:15px; overflow:hidden; box-shadow:0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s; }
    .package-card:hover { transform: translateY(-10px); }
    .package-image { height:200px; background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); display:flex; align-items:center; justify-content:center; font-size:4rem; }
    .package-content { padding:1.5rem; }
    .package-content h3 { margin-bottom:1rem; color:#333; }
    .package-content p { color:#666; margin-bottom:1rem; }
    .price { font-size:1.5rem; color:#667eea; font-weight:bold; margin-bottom:1rem; }
    .btn { display:inline-block; padding:0.8rem 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; text-decoration:none; border-radius:25px; transition:opacity 0.3s; border:none; cursor:pointer; font-size:1rem; }
    .btn:hover { opacity:0.9; }
    </style>
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
            <h2 class="section-title">Our Travel Packages</h2>
            <div class="packages-grid">
                <?php foreach($packages as $pkg): ?>
                <div class="package-card">
                    <div class="package-image"><?= htmlspecialchars($pkg['icon'] ?: '🏖️') ?></div>
                    <div class="package-content">
                        <h3><?= htmlspecialchars($pkg['name']) ?></h3>
                        <p><?= htmlspecialchars($pkg['description']) ?></p>
                        <div class="price">$<?= number_format($pkg['price'],2) ?></div>
                        <a href="login.php" class="btn">Book Now</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>



    </body>
</html>
