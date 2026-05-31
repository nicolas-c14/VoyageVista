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
   GET ID
========================= */

if (!isset($_GET["id"])) {

    die("ID invalide.");

}

$id = intval($_GET["id"]);

$destination =
    getDestinationById($id);

if (!$destination) {

    die("Destination introuvable.");

}

/* =========================
   UPDATE
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    updateDestination(

        $id,

        $_POST["name"],

        $_POST["country"],

        $_POST["description"],

        $_POST["image"],

        $_POST["price"]

    );

    header(
        "Location: dashboard.php"
    );

    exit;

}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>
        Modifier destination
    </title>
    <link rel="icon" href="../assets/images/VoyageVistaLogo.png" type="image/png">

    <link rel="stylesheet"
          href="../assets/css/bootstrap.min.css">

    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>

<body>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container py-5">

    <h1 class="mb-5">

        Modifier destination

    </h1>

    <form method="POST">

        <!-- NAME -->
        <div class="mb-3">

            <label class="form-label">
                Nom
            </label>

            <input type="text"
                   name="name"
                   class="form-control"
                   value="<?= $destination["name"]; ?>"
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
                   value="<?= $destination["country"]; ?>"
                   required>

        </div>

        <!-- DESCRIPTION -->
        <div class="mb-3">

            <label class="form-label">
                Description
            </label>

            <textarea name="description"
                      class="form-control"
                      rows="5"
                      required><?= $destination["description"]; ?></textarea>

        </div>

        <!-- IMAGE -->
        <div class="mb-3">

            <label class="form-label">
                Image
            </label>

            <input type="text"
                   name="image"
                   class="form-control"
                   value="<?= $destination["image"]; ?>"
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
                   value="<?= $destination["price"]; ?>"
                   required>

        </div>

        <!-- BUTTON -->
        <button type="submit"
                class="btn btn-primary">

            Sauvegarder

        </button>

    </form>

</div>

</body>

</html>