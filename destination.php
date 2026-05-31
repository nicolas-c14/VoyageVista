<?php

session_start();

require_once __DIR__ . "/models/destinationModel.php";
require_once __DIR__ . "/models/accommodationModel.php";
/* =========================
   CHECK ID
========================= */

if (!isset($_GET["id"])) {

    die("Destination introuvable.");

}

/* =========================
   GET DESTINATION
========================= */

$id = intval($_GET["id"]);

$destination = getDestinationById($id);

/* =========================
   CHECK DESTINATION
========================= */

if (!$destination) {

    die("Destination inexistante.");

}

/* =========================
   GET ACCOMMODATIONS
========================= */

$accommodations =
    getAccommodationsByDestination(
        $destination["id"]
    );

/* =========================
   SORT ACCOMMODATIONS
========================= */
$sortPrice = $_GET['sort'] ?? '';
if ($sortPrice === 'asc') {
    usort($accommodations, fn($a, $b) => $a['price_per_night'] <=> $b['price_per_night']);
} elseif ($sortPrice === 'desc') {
    usort($accommodations, fn($a, $b) => $b['price_per_night'] <=> $a['price_per_night']);
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>

        <?= $destination["name"]; ?>

        - VoyageVista

    </title>
    <link rel="icon" href="assets/images/VoyageVistaLogo.png" type="image/png">


    <!-- Bootstrap -->
    <link rel="stylesheet"
          href="assets/css/bootstrap.min.css">

    <!-- CSS -->
    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

    <!-- NAVBAR -->
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <!-- DESTINATION -->
    <section class="container my-5">

        <div class="row g-5 align-items-center">

            <!-- IMAGE -->
            <div class="col-md-6">

                <img src="assets/images/<?= $destination["image"]; ?>"
                     class="img-fluid rounded shadow"
                     alt="<?= $destination["name"]; ?>">

            </div>

            <!-- CONTENT -->
            <div class="col-md-6">

                <h1 class="fw-bold mb-3">

                    <?= $destination["name"]; ?>

                </h1>

                <h4 class="text-muted mb-4">

                    <?= $destination["country"]; ?>

                </h4>

                <p class="lead">

                    <?= $destination["description"]; ?>

                </p>

                <h3 class="text-primary mt-4">

                    À partir de
                    <?= $destination["price"]; ?> €
                  
                </h3>

                <a href="destinations.php"
                   class="btn btn-outline-secondary mt-4">

                    Retour aux destinations

                </a>

            </div>

        </div>

        <!-- ACCOMMODATIONS -->
        <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
            <h2 class="mb-0">Hébergements disponibles</h2>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Trier par prix
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-menu-item p-2 d-block text-decoration-none" href="?id=<?= $id; ?>&sort=asc">Prix croissant</a></li>
                    <li><a class="dropdown-menu-item p-2 d-block text-decoration-none" href="?id=<?= $id; ?>&sort=desc">Prix décroissant</a></li>
                </ul>
            </div>
        </div>

        <div class="row g-4">

            <?php foreach($accommodations as $hotel): ?>

                <div class="col-md-4">

                    <div class="card h-100 shadow-sm">

                        <img src="assets/images/<?= $hotel["image"]; ?>"
                            class="card-img-top">

                        <div class="card-body">

                            <h5>

                                <?= $hotel["name"]; ?>

                            </h5>

                            <p>

                                <?= $hotel["type"]; ?>

                            </p>

                            <p>

                                <?= $hotel["description"]; ?>

                            </p>

                            <p class="fw-bold">

                                <?= $hotel["price_per_night"]; ?> €
                                / nuit

                            </p>

                            <?php if(isset($_SESSION["user_id"])): ?>

                                <a href="reservations/book.php?accommodation_id=<?= $hotel["id"]; ?>"
                                class="btn btn-primary">

                                    Réserver

                                </a>

                            <?php else: ?>

                                <a href="login.php"
                                class="btn btn-primary mt-4">

                                    Connectez-vous pour réserver

                                </a>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </section>

    <!-- FOOTER -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

</body>

</html>