<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['reservation_id'])) {
    header('Location: my-reservations.php');
    exit;
}

$resId = intval($_GET['reservation_id']);

// 1. Récupérer les infos pour remettre les places
$stmt = $pdo->prepare("SELECT transport_id, persons FROM reservations WHERE id = ? AND user_id = ?");
$stmt->execute([$resId, $_SESSION['user_id']]);
$res = $stmt->fetch();

if ($res && $res['transport_id']) {
    try {
        $pdo->beginTransaction();

        // Remettre les places disponibles
        $updTransport = $pdo->prepare("UPDATE transports SET available_seats = available_seats + ? WHERE id = ?");
        $updTransport->execute([$res['persons'], $res['transport_id']]);

        // Détacher le transport de la réservation
        $updRes = $pdo->prepare("UPDATE reservations SET transport_id = NULL WHERE id = ?");
        $updRes->execute([$resId]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
    }
}

header('Location: my-reservations.php');
exit;