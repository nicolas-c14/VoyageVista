<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['reservation_id']) || !isset($_GET['activity_id'])) {
    header('Location: my-reservations.php');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM reservation_activities WHERE reservation_id = ? AND activity_id = ?");
$stmt->execute([intval($_GET['reservation_id']), intval($_GET['activity_id'])]);

header('Location: my-reservations.php');
exit;