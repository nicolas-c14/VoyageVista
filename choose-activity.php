<?php

session_start();

require_once __DIR__ . '/config/database.php';

if(!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit;
}

if(!isset($_GET["reservation_id"])) {

    die("Réservation invalide.");
}

$reservationId =
    intval($_GET["reservation_id"]);

$sql = "
SELECT

    reservations.*,

    accommodations.destination_id

FROM reservations

INNER JOIN accommodations
ON reservations.accommodation_id =
accommodations.id

WHERE reservations.id = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([$reservationId]);

$reservation = $stmt->fetch();

if(!$reservation) {

    die("Réservation introuvable.");
}

$sql = "
SELECT *
FROM activities
WHERE destination_id = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $reservation["destination_id"]
]);

$activities = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang='fr'>

<head>

<meta charset='UTF-8'>

<title>
Choix activités
</title>
<link rel="icon" href="assets/images/VoyageVistaLogo.png" type="image/png">

<link
href="assets/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

<h1 class="mb-5">

Choisissez vos activités

</h1>

<div class="row g-4">

<?php foreach($activities as $activity): ?>

<div class="col-md-4">

<div class="card shadow-sm h-100">

<div class="card-body">

<h5>

<?= $activity["name"]; ?>

</h5>

<p>

<?= $activity["description"]; ?>

</p>

<p class="fw-bold">

<?= $activity["price"]; ?> €

</p>

<a

href="save-activity.php?reservation_id=<?= $reservationId; ?>&activity_id=<?= $activity["id"]; ?>"

class="btn btn-primary w-100">

Ajouter activité

</a>

</div>

</div>

</div>

<?php endforeach; ?>

</div>

<div class="mt-5">

<a

href="reservations/my-reservations.php"

class="btn btn-success">

Terminer réservation

</a>

</div>

</div>

</body>

</html>