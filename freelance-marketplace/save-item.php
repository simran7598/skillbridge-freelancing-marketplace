<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('includes/db.php');

$userId = $_SESSION['user_id'];
$itemType = $_GET['type'] ?? '';
$itemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$allowedTypes = ['job', 'freelancer', 'service'];

if (!in_array($itemType, $allowedTypes) || $itemId <= 0) {
    die("Invalid save request.");
}

$sql = "INSERT IGNORE INTO saved_items (user_id, item_type, item_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isi", $userId, $itemType, $itemId);
$stmt->execute();

if ($itemType === 'job') {
    header("Location: job-details.php?id=" . $itemId);
} elseif ($itemType === 'freelancer') {
    header("Location: freelancer-profile.php?id=" . $itemId);
} else {
    header("Location: service-details.php?id=" . $itemId);
}
exit;