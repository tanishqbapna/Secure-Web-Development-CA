<?php
$host = "localhost";
$dbname = "travel_agency";
$username = "root"; // your phpMyAdmin username
$password = "";     // your phpMyAdmin password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
