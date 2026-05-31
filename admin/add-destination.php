<?php

session_start();

require_once __DIR__ . "/../models/destinationModel.php";

/* =========================
   ADMIN CHECK
========================= */

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["user_role"] !== "admin"
) {

    die("Accès interdit.");

}

/* =========================
   FORM SUBMIT
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name =
        trim($_POST["name"]);

    $country =
        trim($_POST["country"]);

    $description =
        trim($_POST["description"]);

    $image =
        trim($_POST["image"]);

    $price =
        trim($_POST["price"]);

    /* =========================
       VALIDATION
    ========================= */

    if (
        !empty($name) &&
        !empty($country) &&
        !empty($description) &&
        !empty($image) &&
        !empty($price)
    ) {

        addDestination(
            $name,
            $country,
            $description,
            $image,
            $price
        );

        header(
            "Location: dashboard.php"
        );

        exit;

    }

}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Ajouter Destination
    </title>
    <link rel="icon" href="../assets/images/VoyageVistaLogo.png" type="image/png">

    <!-- Bootstrap -->
    <link rel="stylesheet"
          href="../assets/css/bootstrap.min.css">

    <!-- CSS -->
    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>

<body>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container py-5">

    <h1 class="mb-5">

        Ajouter une destination

    </h1>

    <!-- FORM -->
    <form method="POST">

        <!-- NAME -->
        <div class="mb-3">

            <label class="form-label">

                Nom

            </label>

            <input type="text"
                   name="name"
                   class="form-control"
                   required>

        </div>

        <!-- COUNTRY -->
        <div class="mb-3">

            <label class="form-label">

                Pays

            </label>

            <input type="text"
                   name="country"
                   class="form-control"
                   required>

        </div>

        <!-- DESCRIPTION -->
        <div class="mb-3">

            <label class="form-label">

                Description

            </label>

            <textarea name="description"
                      rows="5"
                      class="form-control"
                      required></textarea>

        </div>

        <!-- IMAGE -->
        <div class="mb-3">

            <label class="form-label">

                Nom image

            </label>

            <input type="text"
                   name="image"
                   class="form-control"
                   placeholder="paris.jpg"
                   required>

        </div>

        <!-- PRICE -->
        <div class="mb-4">

            <label class="form-label">

                Prix

            </label>

            <input type="number"
                   step="0.01"
                   name="price"
                   class="form-control"
                   required>

        </div>

        <!-- BUTTON -->
        <button type="submit"
                class="btn btn-primary">

            Ajouter destination

        </button>

    </form>

</div>

</body>

</html>