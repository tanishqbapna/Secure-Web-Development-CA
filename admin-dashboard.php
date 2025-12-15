<?php
session_start();
require 'db.php';

$is_logged_in = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? '';


if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header('Location: login.php');
    exit;
}

$action_message = '';

if(isset($_POST['add_package'])){
    $stmt = $pdo->prepare("INSERT INTO packages (name, description, price, icon, duration, destination) VALUES (?,?,?,?,?,?)");
    $stmt->execute([
        $_POST['name'], $_POST['description'], $_POST['price'],
        $_POST['icon'], $_POST['duration'], $_POST['destination']
    ]);
    $action_message = "Package added successfully!";
}

if(isset($_POST['update_package'])){
    $stmt = $pdo->prepare("UPDATE packages SET name=?, description=?, price=?, icon=?, duration=?, destination=? WHERE id=?");
    $stmt->execute([
        $_POST['name'], $_POST['description'], $_POST['price'],
        $_POST['icon'], $_POST['duration'], $_POST['destination'], $_POST['package_id']
    ]);
    $action_message = "Package updated successfully!";
}

if(isset($_POST['delete_package'])){
    $stmt = $pdo->prepare("DELETE FROM packages WHERE id=?");
    $stmt->execute([$_POST['package_id']]);
    $action_message = "Package deleted successfully!";
}

if(isset($_POST['delete_booking'])){
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id=?");
    $stmt->execute([$_POST['booking_id']]);
    $action_message = "Booking deleted successfully!";
}

if(isset($_POST['delete_user'])){
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$_POST['user_id']]);
    $action_message = "User deleted successfully!";
}

if(isset($_POST['add_user'])){
    
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?,?,?,?)");
    $stmt->execute([
        $_POST['username'],
        $_POST['email'],
        $_POST['password'],
        $_POST['role']
    ]);
    $action_message = "User added successfully!";
}

if (isset($_POST['update_user'])) {

    if (!empty($_POST['password'])) {
        $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, password=?, role=? WHERE id=?");
        $stmt->execute([
            $_POST['username'],
            $_POST['email'],
            $_POST['password'],
            $_POST['role'],
            $_POST['user_id']
        ]);

    } else {
        $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
        $stmt->execute([
            $_POST['username'],
            $_POST['email'],
            $_POST['role'],
            $_POST['user_id']
        ]);
    }

    $action_message = "User updated successfully!";
}


$packages = $pdo->query("SELECT * FROM packages ORDER BY id ASC")->fetchAll();
$bookings = $pdo->query("SELECT b.*, p.name AS package_name FROM bookings b JOIN packages p ON b.package_id=p.id ORDER BY b.id ASC")->fetchAll();
$users = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();

?>


<html>
    <head>
    <title>Admin Dashboard - Paradise Travels</title>
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
            <h1>Welcome, <?= htmlspecialchars($username) ?> 👋</h1>

            <?php if($action_message): ?>
                <div class="alert"><?= htmlspecialchars($action_message) ?></div>
            <?php endif; ?>

            <div class="section">
                <h2>Manage Packages</h2>

                <table>
                <tr>
                    <th>ID</th><th>Name</th><th>Description</th><th>Price</th><th>Icon</th><th>Duration</th><th>Destination</th><th>Actions</th>
                </tr>
                <?php foreach($packages as $pkg): ?>
                <tr>
                    <td><?= $pkg['id'] ?></td>
                    <td><?= htmlspecialchars($pkg['name']) ?></td>
                    <td><?= htmlspecialchars($pkg['description']) ?></td>
                    <td>$<?= $pkg['price'] ?></td>
                    <td><?= htmlspecialchars($pkg['icon']) ?></td>
                    <td><?= htmlspecialchars($pkg['duration']) ?></td>
                    <td><?= htmlspecialchars($pkg['destination']) ?></td>
                    <td>
                        <form method="POST" class="form-inline">
                            <input type="hidden" name="package_id" value="<?= $pkg['id'] ?>">
                            <button type="submit" name="delete_package" class="btn" style="background:#e74c3c;">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </table>

                <h3>Add New Package</h3>
                <form method="POST" class="form-inline">
                    <input type="text" name="name" placeholder="Name" required>
                    <input type="text" name="description" placeholder="Description" required>
                    <input type="text" name="price" placeholder="Price" required>
                    <input type="text" name="icon" placeholder="Icon (Emoji)">
                    <input type="text" name="duration" placeholder="Duration">
                    <input type="text" name="destination" placeholder="Destination">
                    <button type="submit" name="add_package" class="btn">Add</button>
                </form>
            </div>

            <div class="section">
                <h2>Manage Bookings</h2>
                <table>
                <tr>
                    <th>ID</th><th>Name</th><th>Package</th><th>Date</th><th>People</th><th>Status</th><th>Total Price</th><th>Actions</th>
                </tr>
                <?php foreach($bookings as $b): ?>
                <tr>
                    <td><?= $b['id'] ?></td>
                    <td><?= htmlspecialchars($b['name']) ?></td>
                    <td><?= htmlspecialchars($b['package_name']) ?></td>
                    <td><?= $b['date'] ?></td>
                    <td><?= $b['people'] ?></td>
                    <td><?= $b['status'] ?></td>
                    <td>$<?= $b['total_price'] ?></td>
                    <td>
                        <form method="POST" class="form-inline">
                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                            <button type="submit" name="delete_booking" class="btn" style="background:#e74c3c;">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </table>
            </div>

            <div class="section">
                <h2>Manage Users</h2>

                <table>
                <tr>
                    <th>ID</thth><th>Username</th><th>Email</th><th>Role</th><th>Actions</th>
                </tr>
                <?php foreach($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['id']) ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>

                    <td>
                        <form method="POST" class="form-inline" style="margin-bottom:8px;">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <input type="text" name="username" placeholder="Username"value="<?= htmlspecialchars($u['username']) ?>" required>
                            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($u['email']) ?>" required>
                            <input type="password" name="password" placeholder="New Password (optional)">

                            <select name="role">
                                <option value="user" <?= $u['role']=='user'?'selected':'' ?>>User</option>
                                <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
                            </select>

                            <button type="submit" name="update_user" class="btn">Update</button>
                        </form>

                        <form method="POST" class="form-inline">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <button type="submit" name="delete_user" class="btn" style="background:#e74c3c;">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </table>

                <h3>Add New User</h3>
                <form method="POST" class="form-inline">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>

                    <select name="role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>

                    <button type="submit" name="add_user" class="btn">Add</button>
                </form>
            </div>
        </div>
    </body>
</html>
