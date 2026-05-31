<?php

session_start();

require_once __DIR__ . "/../models/reservationModel.php";
require_once __DIR__ . "/../config/database.php";

/* =========================
   LOGIN CHECK
========================= */

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;

}

/* =========================
   GET RESERVATIONS
========================= */

$reservations =
    getReservationsByUser(
        $_SESSION["user_id"]
    );

$hasReservations = !empty($reservations);

/* =========================
   FILTRES ET TRIS
========================= */
$statusFilter = $_GET['status'] ?? '';
$sort = $_GET['sort'] ?? 'date_desc';

if ($hasReservations) {
    // Filtrage manuel (si non géré par le model)
    if ($statusFilter) {
        $reservations = array_filter($reservations, function($r) use ($statusFilter) {
            return $r['status'] === $statusFilter;
        });
    }

    // Tri manuel
    usort($reservations, function($a, $b) use ($sort) {
        switch ($sort) {
            case 'price_asc': return $a['total_price'] <=> $b['total_price'];
            case 'price_desc': return $b['total_price'] <=> $a['total_price'];
            case 'date_asc': return strcmp($a['check_in'], $b['check_in']);
            default: return strcmp($b['check_in'], $a['check_in']);
        }
    });
    
    $hasReservations = !empty($reservations);
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>
        Mes réservations
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

            Mes réservations

        </h1>

        <!-- BARRE DE RECHERCHE ET FILTRES -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Filtrer par statut</label>
                        <select name="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : ''; ?>>En attente</option>
                            <option value="confirmed" <?= $statusFilter === 'confirmed' ? 'selected' : ''; ?>>Confirmée</option>
                            <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : ''; ?>>Terminée</option>
                            <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Annulée</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Trier par</label>
                        <select name="sort" class="form-select">
                            <option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : ''; ?>>Date (Récent → Ancien)</option>
                            <option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : ''; ?>>Date (Ancien → Récent)</option>
                            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : ''; ?>>Prix (Croissant)</option>
                            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : ''; ?>>Prix (Décroissant)</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Appliquer les filtres</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">

            <?php if(!$hasReservations): ?>

                <div class="col-12">

                    <div class="alert alert-info mb-0">

                        Vous n'avez pas encore de réservation.

                    </div>

                </div>

            <?php endif; ?>

            <!-- LOOP RESERVATIONS -->
            <?php foreach($reservations as $reservation): ?>

                <?php

                $checkIn =
                    new DateTime(
                        $reservation["check_in"]
                    );

                $checkOut =
                    new DateTime(
                        $reservation["check_out"]
                    );

                $nights =
                    $checkIn->diff($checkOut)->days;

                /* Calcul d'une nouvelle date si la résa est passée
                   (pour permettre la re-réservation au panier) */
                $today = new DateTime("today");
                $cartCheckIn  = $reservation["check_in"];
                $cartCheckOut = $reservation["check_out"];
                if ($checkIn < $today) {
                    $newCheckIn  = (clone $today)->modify("+1 day");
                    $newCheckOut = (clone $newCheckIn)->modify("+{$nights} day");
                    $cartCheckIn  = $newCheckIn->format("Y-m-d");
                    $cartCheckOut = $newCheckOut->format("Y-m-d");
                }

                /* Récupération des activités pour cette réservation */
                $stmtActs = $pdo->prepare("
                    SELECT a.id, a.name, a.price 
                    FROM activities a 
                    JOIN reservation_activities ra ON a.id = ra.activity_id 
                    WHERE ra.reservation_id = ?
                ");
                $stmtActs->execute([$reservation["id"]]);
                $bookedActivities = $stmtActs->fetchAll();
                ?>

                <div class="col-md-4">

                    <div class="card h-100 shadow-sm">

                        <img src="../assets/images/<?= $reservation["image"]; ?>"
                            class="card-img-top">

                        <div class="card-body">

                            <h5>

                                <?= $reservation["accommodation_name"]; ?>
                            </h5>

                            <p>
                                <?= $reservation["destination_name"]; ?>
                            </p>

                            <?php if($reservation["transport_type"]): ?>

                            <p class="mt-2">

                                ✈ Transport :

                                <?= $reservation["transport_type"]; ?>

                                <br>

                                <?= $reservation["departure_city"]; ?>

                                →

                                <?= $reservation["arrival_city"]; ?>
                                
                                <br>
                                <a href="cancel-transport.php?reservation_id=<?= $reservation['id']; ?>" 
                                   class="text-danger small" 
                                   onclick="return confirm('Annuler ce transport ?')">
                                   Annuler le transport
                                </a>

                            </p>

                            <?php endif; ?>

                            <p>
                                <?= $reservation["country"]; ?>
                            </p>

                            <p>

                                📅 Arrivée :
                                <?= $reservation["check_in"]; ?>

                            </p>

                            <p>

                                📅 Départ :
                                <?= $reservation["check_out"]; ?>

                            </p>

                            <p>

                                👥
                                <?= $reservation["persons"]; ?>
                                personne(s)

                            </p>

                            <p>

                                🌙
                                <?= $nights; ?>
                                nuit(s) 

                            </p>

                            <!-- AFFICHAGE DES ACTIVITÉS -->
                            <?php if (!empty($bookedActivities)): ?>
                                <div class="mt-3 p-2 bg-light rounded">
                                    <p class="small mb-1 fw-bold">🎡 Activités :</p>
                                    <ul class="list-unstyled mb-0">
                                        <?php foreach($bookedActivities as $act): ?>
                                            <li class="small d-flex justify-content-between align-items-center mb-1">
                                                <?= $act['name']; ?> (<?= $act['price']; ?>€)
                                                <a href="cancel-activity.php?reservation_id=<?= $reservation['id']; ?>&activity_id=<?= $act['id']; ?>" 
                                                   class="text-danger ms-2"
                                                   title="Retirer l'activité">
                                                   &times;
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <p class="fw-bold text-primary">

                                💰
                                <?= $reservation["total_price"]; ?> €

                            </p>

                            <p>

                                📌 Statut :
                                
                                <?php

                                $statusClass = "bg-secondary";

                                if ($reservation["status"] === "confirmed") {

                                    $statusClass = "bg-success";

                                }

                                elseif ($reservation["status"] === "pending") {

                                    $statusClass = "bg-warning";

                                }

                                elseif ($reservation["status"] === "cancelled") {

                                    $statusClass = "bg-danger";

                                }

                                elseif ($reservation["status"] === "completed") {

                                    $statusClass = "bg-primary";

                                }

                                ?>

                                <span class="badge <?= $statusClass; ?>">

                                    <?= ucfirst($reservation["status"]); ?>

                                </span>

                            </p>

                        </div>

                        <div class="card-footer bg-white border-0">

                            <a href="edit-reservation.php?id=<?= $reservation["id"]; ?>"
                            class="btn btn-outline-primary w-100 mb-2">

                                Modifier

                            </a>

                            <!-- BOUTON AJOUTER AU PANIER -->
                            <form method="POST"
                                  action="../cart/add.php">
                                <input type="hidden"
                                       name="accommodation_id"
                                       value="<?= $reservation["accommodation_id"]; ?>">
                                <input type="hidden"
                                       name="check_in"
                                       value="<?= $cartCheckIn; ?>">
                                <input type="hidden"
                                       name="check_out"
                                       value="<?= $cartCheckOut; ?>">
                                <input type="hidden"
                                       name="persons"
                                       value="<?= $reservation["persons"]; ?>">
                                <button type="submit"
                                        class="btn btn-outline-primary w-100">
                                    🛒 Ajouter au panier
                                </button>
                            </form>

                            <!-- BOUTON ANNULER -->
                            <a href="delete-reservation.php?id=<?= $reservation["id"]; ?>"
                               class="btn btn-outline-danger w-100">
                                Annuler la réservation
                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</body>

</html>