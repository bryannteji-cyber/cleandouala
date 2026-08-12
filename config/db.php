<?php
// Database connection for CleanDouala
// XAMPP default settings

$host = "localhost";
$dbname = "cleandouala";
$username = "root";
$password = "";  // XAMPP default has no password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "<br>Make sure you created the database 'cleandouala' in phpMyAdmin.");
}
?>
