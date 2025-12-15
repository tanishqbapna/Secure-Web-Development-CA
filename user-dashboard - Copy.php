<?php
session_start();
require 'db.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? '';

// Only allow logged-in users
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['user_id']) && $_GET['user_id'] != $_SESSION['user_id']) {
    die('Unauthorized access');
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Ensure email is in session for booking
if(!isset($_SESSION['email'])){
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id=?");
    $stmt->execute([$user_id]);
    $user_email = $stmt->fetchColumn();
    $_SESSION['email'] = $user_email;
}

// --- Handle Booking Submission ---
$booking_message = '';
if(isset($_POST['book_package'])){
    $package_id = $_POST['package_id'];
    $date = $_POST['date'];
    $people = $_POST['people'];
    $requests = $_POST['requests'];

    // Get package price
    $stmt = $pdo->prepare("SELECT price FROM packages WHERE id=?");
    $stmt->execute([$package_id]);
    $package = $stmt->fetch();

    if($package){
        $total_price = $package['price'] * $people;
        $stmt = $pdo->prepare("INSERT INTO bookings (name,email,package_id,date,people,requests,total_price) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([
            $username,
            $_SESSION['email'],
            $package_id,
            $date,
            $people,
            $requests,
            $total_price
        ]);
        $booking_message = "Booking successful! Total Price: $".$total_price;
    } else {
        $booking_message = "Invalid package selected!";
    }
}

// --- Handle Booking Cancellation ---
if(isset($_POST['cancel_booking'])){
    $booking_id = $_POST['booking_id'];
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id=? AND email=?");
    $stmt->execute([$booking_id, $_SESSION['email']]);
    $booking_message = "Booking #$booking_id has been canceled.";
}

// Fetch all packages
$packages = $pdo->query("SELECT * FROM packages ORDER BY id DESC")->fetchAll();

// Fetch user’s bookings
$bookings = $pdo->prepare("SELECT b.*, p.name as package_name FROM bookings b JOIN packages p ON b.package_id=p.id WHERE b.email=? ORDER BY b.id DESC");
$bookings->execute([$_SESSION['email']]);
$user_bookings = $bookings->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard - Paradise Travels</title>
<style>
body { font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f0f2f5; margin:0; padding:0; color:#333; }
.navbar { font-weight:bold; background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); padding:1rem 2rem; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
.nav-content { max-width:1200px; margin:0 auto; display:flex; justify-content:space-between; align-items:center; }
.logo { font-size:1.8rem; font-weight:bold; color:white; }
.nav-links { display:flex; gap:2rem; }
.nav-links a { color:white; text-decoration:none; transition:opacity 0.3s; }
.nav-links a:hover { opacity:0.8; }
.container { max-width:1200px; margin:2rem auto; padding:0 1rem; }
h2 { color:#667eea; margin-bottom:1rem; }
.table-container { overflow-x:auto; margin-bottom:2rem; }
table { width:100%; border-collapse:collapse; background:white; border-radius:10px; overflow:hidden; }
table th, table td { padding:0.8rem; border-bottom:1px solid #ddd; text-align:left; }
table th { background:#667eea; color:white; }
.btn { padding:0.5rem 1rem; border:none; border-radius:5px; cursor:pointer; color:white; background:#764ba2; }
.btn:hover { opacity:0.9; }
.btn-danger { background:#e74c3c; }
.form-inline { display:flex; gap:0.5rem; flex-wrap:wrap; margin-bottom:1rem; }
input, select, textarea { padding:0.5rem; border-radius:5px; border:1px solid #ccc; }
.alert { background:#27ae60; color:white; padding:0.5rem 1rem; border-radius:5px; margin-bottom:1rem; }
.package-card { background:white; border-radius:10px; padding:1rem; margin-bottom:1rem; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
.package-card h3 { margin-bottom:0.5rem; color:#333; }
.package-card p { color:#666; margin-bottom:0.5rem; }
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

<h1>Welcome! <?= htmlspecialchars($username) ?> 👋</h1>


<?php if($booking_message): ?>
    <div class="alert"><?= htmlspecialchars($booking_message) ?></div>
<?php endif; ?>

<!-- PACKAGES SECTION -->
<h2>Available Packages</h2>
<?php foreach($packages as $pkg): ?>
<div class="package-card">
    <h3><?= htmlspecialchars($pkg['name']) ?> (<?= htmlspecialchars($pkg['icon']) ?>)</h3>
    <p><?= htmlspecialchars($pkg['description']) ?></p>
    <p>Duration: <?= htmlspecialchars($pkg['duration']) ?> | Destination: <?= htmlspecialchars($pkg['destination']) ?></p>
    <p>Price: $<?= $pkg['price'] ?></p>
    <form method="POST" class="form-inline">
        <input type="hidden" name="package_id" value="<?= $pkg['id'] ?>">
        <input type="date" name="date" required>
        <input type="number" name="people" min="1" value="1" required>
        <textarea name="requests" placeholder="Special Requests"></textarea>
        <button type="submit" name="book_package" class="btn">Book Now</button>
    </form>
</div>
<?php endforeach; ?>

<!-- USER BOOKINGS -->
<h2>Your Bookings</h2>
<div class="table-container">
<table>
<tr>
    <th>Package</th><th>Date</th><th>People</th><th>Requests</th><th>Status</th><th>Total Price</th><th>Action</th>
</tr>
<?php foreach($user_bookings as $b): ?>
<tr>
    <td><?= htmlspecialchars($b['package_name']) ?></td>
    <td><?= $b['date'] ?></td>
    <td><?= $b['people'] ?></td>
    <td><?= htmlspecialchars($b['requests']) ?></td>
    <td><?= $b['status'] ?></td>
    <td>$<?= $b['total_price'] ?></td>
    <td>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
            <button type="submit" name="cancel_booking" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>
</div>

</div>
</body>
</html>
