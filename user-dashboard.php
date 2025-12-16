<?php
session_start();
require 'db.php';

$is_logged_in = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? '';

$username = $_SESSION['username'];
$userId = $_GET['user_id'] ?? null;

if ($userId === null) {
    die('User ID missing');
}

$sql = "SELECT * FROM users WHERE id = $userId";
$result = $pdo->query($sql);
$user = $result->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('User not found');
}

$user_id = $_SESSION['user_id'];
$username = $user['username'];


$booking_message = '';
if(isset($_POST['book_package'])){
    $package_id = $_POST['package_id'];
    $date = $_POST['date'];
    $people = $_POST['people'];
    $requests = $_POST['requests'];

    $stmt = $pdo->prepare("SELECT price FROM packages WHERE id=?");
    $stmt->execute([$package_id]);
    $package = $stmt->fetch();

    if($package){
        $total_price = $package['price'] * $people;
        
        $stmt = $pdo->prepare("
    INSERT INTO bookings
    (name, package_id, date, people, requests, total_price)
    VALUES (?,?,?,?,?,?)
");

$stmt->execute([
    $username,
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


if(isset($_POST['cancel_booking'])){
    $booking_id = $_POST['booking_id'];
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->execute([$booking_id]);    
    
    $booking_message = "Booking #$booking_id has been canceled.";
}



$packages = $pdo->query("SELECT * FROM packages ORDER BY id DESC")->fetchAll();



$bookings = $pdo->prepare("
    SELECT b.*, p.name AS package_name
    FROM bookings b
    JOIN packages p ON b.package_id = p.id
    WHERE b.name = ?
    ORDER BY b.id DESC
");
$bookings->execute([$username]);
$user_bookings = $bookings->fetchAll();
?>

<html>
<head>
<title>User Dashboard - Paradise Travels</title>
<style>
input, select, textarea { padding:0.5rem; border-radius:5px; border:1px solid #ccc; }
</style>
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

    <h1>Welcome, <?= htmlspecialchars($user['username']) ?></h1>

    <?php if($booking_message): ?>
        <div class="alert"><?= htmlspecialchars($booking_message) ?></div>
    <?php endif; ?>

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

    <h2>Your Bookings</h2>
    <div class="container">
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
