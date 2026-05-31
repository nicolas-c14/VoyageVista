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
   GET DESTINATIONS
========================= */

$destinations = getAllDestinations();

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>
        Admin Dashboard
    </title>
    <link rel="icon" href="assets/images/VoyageVistaLogo.png" type="image/png">
    <link rel="icon" href="../assets/images/VoyageVistaLogo.png" type="image/png">


    <link rel="stylesheet"
          href="../assets/css/bootstrap.min.css">

    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>

<body>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-5">

        <h1>
            Panel Administrateur
        </h1>

        <a href="add-destination.php"
           class="btn btn-primary">

            Ajouter destination

        </a>

        <a href="../destinations.php"
           class="btn btn-secondary">

            Retourner aux destinations

        </a>

        <a href="../admin-users.php"
           class="btn btn-secondary">

            Gestion des rôles

        </a>

    </div>

    <!-- TABLE -->
    <table class="table table-bordered table-hover">

        <thead class="table-dark">

            <tr>

                <th>ID</th>
                <th>Image</th>
                <th>Nom</th>
                <th>Pays</th>
                <th>Prix</th>
                <th>Actions</th>

            </tr>

        </thead>

        <tbody>

            <?php foreach($destinations as $destination): ?>

                <tr>

                    <td>
                        <?= $destination["id"]; ?>
                    </td>

                    <td>

                        <img src="../assets/images/<?= $destination["image"]; ?>"
                             width="120">

                    </td>

                    <td>
                        <?= $destination["name"]; ?>
                    </td>

                    <td>
                        <?= $destination["country"]; ?>
                    </td>

                    <td>
                        <?= $destination["price"]; ?> €
                    </td>

                    <td>

                        <!-- EDIT -->
                        <a href="edit-destination.php?id=<?= $destination["id"]; ?>"
                           class="btn btn-warning btn-sm">

                            Modifier

                        </a>

                        <!-- DELETE -->
                        <a href="delete-destination.php?id=<?= $destination["id"]; ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Supprimer cette destination ?')">

                            Supprimer

                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</div>

</body>

</html>