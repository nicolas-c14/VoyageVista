<?php

session_start();

require_once __DIR__ . "/../models/reservationModel.php";
require_once __DIR__ . "/../models/accommodationModel.php";

/* =========================
   LOGIN CHECK
========================= */

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;

}

/* =========================
   GET RESERVATION
========================= */

if (!isset($_GET["id"])) {

    die("Réservation introuvable.");

}

$reservationId =
    intval($_GET["id"]);

$reservation =
    getReservationById(
        $reservationId,
        $_SESSION["user_id"]
    );

if (!$reservation) {

    die("Réservation inexistante.");

}

/* =========================
   GET ACCOMMODATION
========================= */

$accommodation =
    getAccommodationById(
        $reservation["accommodation_id"]
    );

/* =========================
   SUBMIT
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $checkIn =
        $_POST["check_in"];

    $checkOut =
        $_POST["check_out"];

    $persons =
        intval($_POST["persons"]);

    /* =========================
       DATE CHECK
    ========================= */

    if ($checkOut <= $checkIn) {

        $error =
            "Dates invalides.";

    }

    /* =========================
       AVAILABILITY
    ========================= */

    elseif (
        !isAccommodationAvailable(
            $reservation["accommodation_id"],
            $checkIn,
            $checkOut,
            $reservationId
        )
    ) {

        $error =
            "Hébergement indisponible.";

    }

    else {

        /* =========================
           CALCUL NIGHTS
        ========================= */

        $start =
            new DateTime($checkIn);

        $end =
            new DateTime($checkOut);

        $nights =
            $start->diff($end)->days;

        /* =========================
           TOTAL PRICE
        ========================= */

        $totalPrice =
            $nights *
            $accommodation["price_per_night"] *
            $persons;

        /* =========================
           UPDATE
        ========================= */

        updateReservation(
            $reservationId,
            $checkIn,
            $checkOut,
            $persons,
            $totalPrice
        );

        header(
            "Location: my-reservations.php"
        );

        exit;

    }

}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>
        Modifier réservation
    </title>
    <link rel="icon" href="../assets/images/VoyageVistaLogo.png" type="image/png">

    <link rel="stylesheet"
          href="../assets/css/bootstrap.min.css">

</head>

<body>

<div class="container py-5">

    <h1 class="mb-5">

        Modifier réservation

    </h1>

    <?php if(isset($error)): ?>

        <div class="alert alert-danger">

            <?= $error; ?>

        </div>

    <?php endif; ?>

    <form method="POST">

        <!-- CHECK IN -->
        <div class="mb-3">

            <label class="form-label">

                Arrivée

            </label>

            <input type="date"
                   name="check_in"
                   class="form-control"
                   value="<?= $reservation["check_in"]; ?>"
                   required>

        </div>

        <!-- CHECK OUT -->
        <div class="mb-3">

            <label class="form-label">

                Départ

            </label>

            <input type="date"
                   name="check_out"
                   class="form-control"
                   value="<?= $reservation["check_out"]; ?>"
                   required>

        </div>

        <!-- PERSONS -->
        <div class="mb-4">

            <label class="form-label">

                Personnes

            </label>

            <input type="number"
                   name="persons"
                   class="form-control"
                   value="<?= $reservation["persons"]; ?>"
                   min="1"
                   required>

        </div>

        <button type="submit"
                class="btn btn-primary">

            Sauvegarder

        </button>

    </form>

</div>

</body>

</html>