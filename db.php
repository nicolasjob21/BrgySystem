<?php
function connectToDatabase() {
    $host = "localhost";
    $username = "root";  // Default username for XAMPP
    $password = "";      // Default password for XAMPP (blank)
    $dbname = "brgy45_medsdb";  // Change this to your actual database name

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Set PDO error mode to exception
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}