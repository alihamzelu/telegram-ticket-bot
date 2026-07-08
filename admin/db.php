<?php
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=ticketbot;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("❌ Connection Error: " . $e->getMessage());
}
?>