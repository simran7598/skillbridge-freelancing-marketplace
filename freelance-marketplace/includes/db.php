<?php
$host = "127.0.0.1";
$dbname = "freelance_marketplace";
$username = "root";
$password = "";
$port = 3307;

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>