<?php

/* India Standard Time */
date_default_timezone_set("Asia/Kolkata");

$host = "localhost";
$db = "smart_attendance";
$user = "root";
$pass = "";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    /* Set MySQL session timezone to IST */
    $pdo->exec("SET time_zone = '+05:30'");

} catch (PDOException $e) {

    die(
        "Database connection failed. Run install.php first. " .
        htmlspecialchars($e->getMessage())
    );
}
?>