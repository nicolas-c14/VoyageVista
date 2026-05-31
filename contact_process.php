<?php
session_start();
require_once __DIR__ . '/config/database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $message]);
        
        // On peut rediriger avec un message de succès
        header("Location: index.php?contact_success=1");
        exit;
    }
}

header("Location: index.php");
exit;