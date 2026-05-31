<?php

session_start();

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/login.php');
    exit;
}

if (!isset($_GET['reservation_id'])) {
    die('Réservation invalide.');
}

$reservationId = intval($_GET['reservation_id']);

$sql = "
SELECT
    reservations.*,
    accommodations.destination_id
FROM reservations
JOIN accommodations
ON reservations.accommodation_id = accommodations.id
WHERE reservations.id = ?
AND reservations.user_id = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$reservationId, $_SESSION['user_id']]);

$reservation = $stmt->fetch();

if(!$reservation) {
    die("Réservation introuvable.");
}

$sql = "
SELECT *
FROM transports
WHERE destination_id = ?
AND available_seats >= ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $reservation['destination_id'],
    $reservation['persons']
]);

$transports = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<title>Choix transport</title>
<link rel="icon" href="assets/images/VoyageVistaLogo.png" type="image/png">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

<h1 class="mb-5">
Choisissez votre transport
</h1>

<div class="row g-4">

<?php foreach($transports as $transport): ?>

<div class="col-md-4">

<div class="card shadow-sm h-100">

<img
src="assets/images/<?= $transport['image']; ?>"
class="card-img-top">

<div class="card-body">

<h5>
<?= $transport['type']; ?>
</h5>

<p>
<?= $transport['departure_city']; ?>
→
<?= $transport['arrival_city']; ?>
</p>

<p>
Départ :
<?= $transport['departure_date']; ?>
</p>

<p>
<?= $transport['price']; ?> €
</p>

<p class="text-success">
<?= $transport['available_seats']; ?>
places restantes
</p>

<a
href="<?= APP_URL; ?>/save-transport.php?reservation_id=<?= $reservation['id']; ?>&transport_id=<?= $transport['id']; ?>"
class="btn btn-primary w-100">

Choisir ce transport

</a>

</div>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

</body>
</html>