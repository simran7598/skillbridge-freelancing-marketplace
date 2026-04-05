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
    die("Invalid remove request.");
}

$sql = "DELETE FROM saved_items WHERE user_id = ? AND item_type = ? AND item_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isi", $userId, $itemType, $itemId);
$stmt->execute();

header("Location: saved-items.php");
exit;