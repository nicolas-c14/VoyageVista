<?php
session_start();

require_once __DIR__ . "/../config/app.php";
require_once __DIR__ . "/../models/accommodationModel.php";
require_once __DIR__ . "/../models/reservationModel.php";
require_once __DIR__ . "/../models/cartModel.php";

/* =========================
   LOGIN CHECK
========================= */
if (!isset($_SESSION["user_id"])) {
    header("Location: " . APP_URL . "/login.php");
    exit;
}

/* =========================
   GET ACCOMMODATION
========================= */
if (!isset($_GET["accommodation_id"])) {
    die("Hébergement invalide.");
}
$accommodationId =
    intval($_GET["accommodation_id"]);
$accommodation =
    getAccommodationById(
        $accommodationId
    );
if (!$accommodation) {
    die("Hébergement introuvable.");
}

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

    $action =
        $_POST["action"] ?? "reserve";

    /* =========================
       DATE VALIDATION
    ========================= */

    if ($checkOut <= $checkIn) {

        $error =
            "La date de départ doit être après la date d'arrivée.";

    }

    elseif ($persons <= 0) {

        $error =
            "Nombre de personnes invalide.";

    }

    elseif ($checkIn < date("Y-m-d")) {

        $error =
            "La date d'arrivée est invalide.";

    }

    /* =========================
       AVAILABILITY CHECK
    ========================= */

    elseif (
        !isAccommodationAvailable(
            $accommodationId,
            $checkIn,
            $checkOut
        )
    ) {

        $error =
            "Cet hébergement n'est pas disponible pour ces dates.";

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
           ADD TO CART
        ========================= */

        if ($action === "cart") {

            addToCart(

                $_SESSION["user_id"],

                $accommodationId,

                $checkIn,

                $checkOut,

                $persons

            );

            header(
                "Location: " .
                APP_URL .
                "/cart/index.php?added=1"
            );

            exit;

        }

        /* =========================
           DIRECT RESERVATION
        ========================= */

        $reservationId =
            addReservation(

                $_SESSION["user_id"],

                $accommodationId,

                $checkIn,

                $checkOut,

                $persons,

                $totalPrice

            );

        header(
            "Location: " .
            APP_URL .
            "/choose-transport.php?reservation_id=" .
            $reservationId
        );

        exit;

    }

}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réserver</title>
    <link rel="icon"
          href="assets/images/VoyageVistaLogo.png"
          type="image/png">
    <link rel="stylesheet"
          href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . "/../includes/navbar.php"; ?>
<div class="container py-5">
    <h1 class="mb-5">
        Réserver :
        <?= $accommodation["name"]; ?>
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
                Date d'arrivée
            </label>
            <input type="date"
                   name="check_in"
                   class="form-control"
                   required>
        </div>
        <!-- CHECK OUT -->
        <div class="mb-3">
            <label class="form-label">
                Date de départ
            </label>
            <input type="date"
                   name="check_out"
                   class="form-control"
                   required>
        </div>
        <!-- PERSONS -->
        <div class="mb-4">
            <label class="form-label">
                Nombre de personnes
            </label>
            <input type="number"
                   name="persons"
                   class="form-control"
                   min="1"
                   required>
        </div>
        <!-- BUTTONS -->
        <div class="d-flex gap-2">
            <button type="submit"
                    name="action"
                    value="reserve"
                    class="btn btn-primary">
                Confirmer réservation
            </button>
        </div>
    </form>
</div>
</body>
</html>