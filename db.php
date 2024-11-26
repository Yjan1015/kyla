<?php
// Database connection using PDO
$host = 'DESKTOP-6PCKIHS\SQLEXPRESS'; // Your database host
$db = 'kyla'; // Your database name
try {
    $pdo = new PDO("sqlsrv:Server=$host;Database=$db",);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}
?>
