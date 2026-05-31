<?php
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Accès réservé aux administrateurs.");
}

// Mise à jour du rôle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['role'])) {
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$_POST['role'], intval($_POST['user_id'])]);
    $success = "Rôle mis à jour avec succès.";
}

$users = $pdo->query("SELECT id, first_name, last_name, email, role FROM users ORDER BY last_name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Utilisateurs - Admin</title>
    <link rel="icon" href="assets/images/VoyageVistaLogo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/navbar.php'; ?>
    <div class="container py-5">
        <h1>Gestion des rôles utilisateurs</h1>
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?= $success; ?></div>
        <?php endif; ?>

        <div class="table-responsive mt-4">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Nom / Prénom</th>
                        <th>Email</th>
                        <th>Rôle actuel</th>
                        <th>Modifier le rôle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['last_name'] . ' ' . $u['first_name']); ?></td>
                        <td><?= htmlspecialchars($u['email']); ?></td>
                        <td><span class="badge bg-info"><?= $u['role']; ?></span></td>
                        <td>
                            <?php if($u['id'] != $_SESSION['user_id']): ?>
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="user_id" value="<?= $u['id']; ?>">
                                <select name="role" class="form-select form-select-sm" style="width: auto;">
                                    <option value="client" <?= $u['role'] === 'client' ? 'selected' : ''; ?>>Client</option>
                                    <option value="agency" <?= $u['role'] === 'agency' ? 'selected' : ''; ?>>Agence</option>
                                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Changer</button>
                            </form>
                            <?php else: ?>
                            <small class="text-muted">Vous ne pouvez pas modifier votre propre rôle.</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>